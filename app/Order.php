<?php namespace App;

use App\Collections\OrderCollection;
use App\Decorators\Order\ApprovedOrder;
use App\Decorators\Order\CancelledOrder;
use App\Decorators\Order\ClonedOrder;
use App\Decorators\Order\DiscardedOrder;
use App\Decorators\Order\DraftOrder;
use App\Decorators\Order\FulfilledOrder;
use App\Decorators\Order\PendingOrder;
use App\Decorators\Order\RejectedOrder;
use App\Decorators\Order\RescheduledOrder;
use App\Decorators\Order\SubmittedOrder;
use App\Queries\OrderProductSummaryQuery;
use App\Queries\OrderSummaryQuery;
use App\Scopes\CancelledScope;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
	public $timestamps = true;

	protected $table = 'order';
	protected $guarded = [];
	protected $Summary;
	protected $ProductSummary;
	protected $appends = [
		'full_id',
		'pickup_on',
		'readable_status'
	];

	const STATUS_DRAFT = 'draft';
	const STATUS_PENDING_APPROVAL = 'pending_approval';
	const STATUS_PENDING_PICKUP = 'pending_pickup';
	const STATUS_FULFILLED = 'fulfilled';
	const STATUS_CANCELLED = 'cancelled';
	const STATUS_REJECTED = 'rejected';

	protected static $OrderSummaries;

	public static function boot() {
		parent::boot();
		// static::addGlobalScope(new CancelledScope);
	}

	public function Agency() {
		return $this->belongsTo(Agency::class, 'agency_id', 'id', 'Agency');
	}

	public function Item() {
		return $this->hasMany(OrderItem::class, 'order_id', 'id', 'Item');
	}

	public function ApprovedItem() {
		return $this->hasMany(OrderItem::class, 'order_id', 'id', 'Item')
				->where('flag_approved', 1);
	}

	public function Note() {
		return $this->morphMany(Note::class, 'notable', 'model', 'model_id')
					->orderBy('created_at', 'DESC');
		// hasMany(OrderNote::class, 'order_id', 'id', 'Note')
		// 			->orderBy('created_at', 'DESC')
		// 			->with(['Author']);
	}

	public function Child() {
		return $this->hasMany(OrderChild::class, 'order_id', 'id', 'Child');
	}

	public function ApprovedChild() {
		return $this->hasMany(OrderChild::class, 'order_id', 'id', 'Child')
				->select('order_child.*')
				->join('order_item', 'order_item.order_child_id', '=', 'order_child.id')
				->where('order_item.flag_approved', 1);
	}

	public function PickupDate() {
		return $this->belongsTo(PickupDate::class, 'pickup_date_id', 'id', 'PickupDate')->withTrashed();
	}


	public function getPendingChildrenAttribute() {
		return $this->Child->filter(function($Child) {
			return !$Child->Item->deleted_at && !$Child->Item->flag_approved;
		});
	}

	public function getApprovedChildrenAttribute() {
		return $this->Child->filter(function($Child) {
			return !$Child->Item->deleted_at && $Child->Item->flag_approved;
		});
	}

	public function newCollection(array $models = []) {
		return new OrderCollection($models);
	}

	public function addChild(Child $Child) {
		return OrderChild::firstOrCreate([
			'order_id' => $this->id,
			'child_id' => $Child->id,
		]);
	}

	/**
	 * Add an item to an order.
	 * This item is *not* related to a child by default -- but can be later
	 *
	 * @param integer $id      Product.id for the product this order item references
	 * @param integer $quantity Quantity of product being added to the order
	 *
	 * @return OrderItem	the created order item.
	 */
	public function addItem($id, $quantity) {
		return OrderItem::create([
			'order_id' => $this->id,
			'product_id' => $id,
			'quantity' => $quantity,
		]);
	}

	public function getSummaryAttribute() {
		if ( ! $this->Summary) {
			$this->Summary = $this->generateSummary();
		}

		return $this->Summary;
	}

	public function AggregateItems() {
		return $this->ApprovedItem->reduce(function($Aggregate, $Item) {
			if ( ! $OrderItem = $Aggregate->get($Item->product_id)) {
				return $Aggregate->put($Item->product_id, $Item->fresh());
			}

			$OrderItem->quantity += $Item->quantity;
			return $Aggregate;
		}, collect());
	}

	public function getProductSummaryAttribute() {
		if (! $this->ProductSummary) {
			$this->ProductSummary = $this->generateProductSummary();
		}

		return $this->ProductSummary;
	}

	public function getPickupOnAttribute() {
		return $this->PickupDate->pickup_date ?? null;
	}

	public function getFullIdAttribute() {
		return str_pad($this->Agency->id_prefix ?? $this->Agency->id, 5, '0', STR_PAD_LEFT) . "-{$this->id}";
	}

	public function getReadableStatusAttribute() {
		return $this->readableStatus();
	}

	public function readableStatus() {
		return static::orderStatusText($this->order_status);
	}

	public function isDraft() {
		return $this->order_status == static::STATUS_DRAFT;
	}

	public function isPending() {
		return $this->order_status == static::STATUS_PENDING_APPROVAL;
	}

	public function isApproved() {
		return $this->order_status == static::STATUS_PENDING_PICKUP;
	}

	public function isRejected() {
		return $this->order_status == static::STATUS_REJECTED;
	}

	public function isCancelled() {
		return $this->order_status == static::STATUS_CANCELLED;
	}

	public function isFulfilled() {
		return $this->order_status == static::STATUS_FULFILLED;
	}

	/* Decorator Factories */
	public function draft() {
		return DraftOrder::make($this);
	}

	public function approve() {
		return ApprovedOrder::make($this);
	}

	public function cancel() {
		return CancelledOrder::make($this);
	}

	public function clone() {
		return ClonedOrder::make($this)->clone();
	}

	public function discard() {
		return DiscardedOrder::make($this)->discard();
	}

	public function fulfill() {
		return FulfilledOrder::make($this)->save();
	}

	public function reject() {
		return RejectedOrder::make($this);
	}

	public function reschedule() {
		return RescheduledOrder::make($this);
	}

	public function submit() {
		return SubmittedOrder::make($this)->submit();
	}

	public function awaitApproval() {
		return PendingOrder::make($this);
	}
	/* End Decorator Factories */

	public function canBeSubmitted() {
		if ( ! $this->isDraft()) return false;
		if ( ! $this->pickup_date_id) return false;
		if ($this->PendingChildren->count() == 0) return false;

		$incompleteChildren = $this->PendingChildren->filter(function($Child) {
			return ! $Child->Item;
		});

		if ($incompleteChildren->count() > 0) return false;

		return true;
	}

	protected function generateSummary() {
		static::$OrderSummaries = OrderSummaryQuery::create()->getSummary();
		return static::$OrderSummaries->get($this->id) ?? null;
	}

	protected function generateProductSummary() {
		return OrderProductSummaryQuery::create()->orderIds([ $this->id ])->getSummary();
	}

	public static function getStatuses() {
		$statuses = [
			static::STATUS_DRAFT,
			static::STATUS_PENDING_APPROVAL,
			static::STATUS_PENDING_PICKUP,
			static::STATUS_FULFILLED,
			static::STATUS_CANCELLED,
			static::STATUS_REJECTED,
		];

		$status_map = [];
		foreach ($statuses as $s) {
			$status_map[$s] = static::orderStatusText($s);
		}

		return $status_map;
	}

	public static function orderStatusText($status) {
		switch ($status) {
			case static::STATUS_DRAFT:
				return 'Draft';

			case static::STATUS_PENDING_APPROVAL:
				return 'Pending Approval';

			case static::STATUS_PENDING_PICKUP:
				return 'Approved';

			case static::STATUS_FULFILLED:
				return 'Fulfilled';

			case static::STATUS_CANCELLED:
				return 'Cancelled';

			case static::STATUS_REJECTED:
				return 'Rejected';

			default:
				return 'Unknown Status';
		}
	}
}
