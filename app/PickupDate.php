<?php

namespace App;

use App\Collections\PickupDates as PickupDatesCollection;
use App\Decorators\PickupDate\ReconciledPickup;
use App\Decorators\PickupDate\RescheduledPickup;
use App\Jobs\GenerateLabels;
use App\Jobs\GenerateManifest;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PickupDate extends Model {
	public static $approvedOrderCounts;
	public static $pendingOrderCounts;

	protected $table = 'pickup_date';
	protected $guarded = [];

	use SoftDeletes;

	protected $casts = [
		'pickup_date' => 'datetime',
		'reconciled_at' => 'datetime',
	];

	protected $appends = [
		'orders_approved', // approved and pending pickup
		'orders_pending', // not approved, pending approval
		'orders_pending_export', // approved orders, awaiting export for fulfillment
	];

	protected $dates = [
		'pickup_date',
		'deleted_at',
		'reconciled_at',
	];

	const MIN_ORDER_LEAD_TIME_HRS = 113; // Pickups are on Tuesdays @ 10am. Last order can be submitted on the previous Thursday @ 5pm. That's 113 hours.

	public function Order() {
		return $this->hasMany(Order::class, 'pickup_date_id', 'id', 'Order');
	}

	public function ApprovedOrder() {
		return $this->Order()->where('order_status', Order::STATUS_PENDING_PICKUP);
	}

	public function PendingOrder() {
		return $this->Order()->where('order_status', Order::STATUS_PENDING_APPROVAL);
	}

	public function Fulfillment() {
		return $this->hasMany(Fulfillment::class, 'pickup_date_id', 'id');
	}

	public function ManifestPdf() {
		return $this->morphOne(PdfAsset::class, 'pdfable', 'related_model', 'related_id')
					->where('asset_type', PdfAsset::TYPE_MANIFEST)
					->orderBy('created_at', 'DESC');
	}

	public function AllManifestPdfs() {
		return $this->morphMany(PdfAsset::class, 'pdfable', 'related_model', 'related_id')
					->where('asset_type', PdfAsset::TYPE_MANIFEST)
					->orderBy('created_at', 'DESC');
	}

	public function LabelsPdf() {
		return $this->morphOne(PdfAsset::class, 'pdfable', 'related_model', 'related_id')
					->where('asset_type', PdfAsset::TYPE_LABELS)
					->orderBy('created_at', 'DESC');
	}

	public function AllLabelPdfs() {
		return $this->morphMany(PdfAsset::class, 'pdfable', 'related_model', 'related_id')
					->where('asset_type', PdfAsset::TYPE_LABELS)
					->orderBy('created_at', 'DESC');
	}

	public function getOrdersApprovedAttribute() {
		return static::getOrderApprovedCount($this->id);
		// return $this->Order()->where('order_status', Order::STATUS_PENDING_PICKUP)->count();
	}

	static public function getOrderApprovedCount(int $pickupID) {
		if (null == static::$approvedOrderCounts) {
			static::cacheOrderApprovedCounts();	
		}

		if (isset(static::$approvedOrderCounts[ $pickupID ])) {
			return static::$approvedOrderCounts[ $pickupID ];
		}

		return 0;
	}

	static public function cacheOrderApprovedCounts() {
		static::$approvedOrderCounts = Order::with('PickupDate')
										->where('order_status', Order::STATUS_PENDING_PICKUP)
										->get()
										->reduce(function($carry, $order) {
											if ( ! isset($carry[ $order->PickupDate->id ])) {
												$carry[ $order->PickupDate->id ] = 0;
											}

											$carry[ $order->PickupDate->id ] += 1;
											return $carry;
										}, []);
	}

	public function getOrdersPendingAttribute() {
		return static::getOrderPendingCounts($this->id);

		// return $this->Order()->where('order_status', Order::STATUS_PENDING_APPROVAL)->count();
	}

	static public function getOrderPendingCounts(int $pickupID) {
		if (null == static::$pendingOrderCounts) {
			static::cacheOrderPendingCounts();	
		}

		if (isset(static::$pendingOrderCounts[ $pickupID ])) {
			return static::$pendingOrderCounts[ $pickupID ];
		}

		return 0;
	}

	static public function cacheOrderPendingCounts() {
		static::$pendingOrderCounts = Order::with('PickupDate')
		->where('order_status', Order::STATUS_PENDING_APPROVAL)
		->get()
		->reduce(function($carry, $order) {
			if ( ! isset($carry[ $order->PickupDate->id ])) {
				$carry[ $order->PickupDate->id ] = 0;
			}

			$carry[ $order->PickupDate->id ] += 1;
			return $carry;
		}, []);
	}

	public function getOrdersPendingExportAttribute() {
		return Fulfillment::getUnexportedOrders($this)->count();
	}

	public function reschedule() {
		return RescheduledPickup::make($this);
	}

	public function reconcile(array $order_ids) {
		return ReconciledPickup::make($this)->fulfillOrders($order_ids)->save();
	}

	public function newCollection(array $models = []) {
		return new PickupDatesCollection($models);
	}

	public function isReconciled() {
		return ! ! $this->reconciled_at;
	}

	/**
	 * Return a collection of pickup dates, keyed by the date for pickup.
	 *
	 * Each item is a collection of all the pickups available for that date.
	 * (This allows multiple time and location pickups to be scheduled on any single date)
	 *
	 * @param  string $month the Year-month for which to get the available pickups. Formatted as 'Y-m' (Eg, 2017-08)
	 *
	 * @return Collection<Collection<PickupDate>>	Collection of Pickup dates, keyed by pickup date
	 */
	public static function getPickupDatesByDate($month) {
		[ $year, $month ] = explode('-', $month);

		$Query = static::with('Order')
					->where(DB::raw('YEAR(pickup_date)'), $year)
					->where(DB::raw('MONTH(pickup_date)'), $month);

		$Dates = $Query->get();

		$byDate = [];

		foreach ($Dates as $Date) {
			$date_str = $Date->pickup_date->format('Y-m-d');

			if ( ! isset($byDate[$date_str])) {
				$byDate[$date_str] = [];
			}

			$byDate[$date_str] [] = $Date;
		}

		foreach ($byDate as &$dates) {
			$dates = collect($dates);
		}
		unset($dates);

		return collect($byDate);
	}

	public function generatePdfs() {
		$this->generateLabels();
		$this->generateManifest();
	}

	public function generateLabels($filename = null, $silent = null) {
		$Job = new GenerateLabels($this, $filename);

		if ($silent) {
			$Job->silent($silent);
		}

		return dispatch($Job);
	}

	public function generateManifest() {
		return dispatch(new GenerateManifest($this));
	}

	public function generateManifestFilename() {
		return $this->pickup_date->format('Y/m-d') . '/' . carbon()->format('Ymd.His') . '.manifest.' . uniqid('', true) . '.pdf';
	}

	public function generatePackingLabelFilename() {
		return $this->pickup_date->format('Y/m-d') . '/' . carbon()->format('Ymd.His') . '.labels.' . uniqid('', true) . '.pdf';
	}

	public function storePackingLabels(PdfAsset $Asset) {
		if ($ExistingAssets = $this->AllLabelPDFs) {
			PdfAsset::destroy($ExistingAssets->pluck('id')->all());
		}

		$this->LabelsPDF()->save($Asset);
		$this->load('LabelsPDF');

		return $this;
	}

	public function storeOrderManifest(PdfAsset $Asset) {
		if ($ExistingAssets = $this->AllManifestPDFs) {
			PdfAsset::destroy($ExistingAssets->pluck('id')->all());
		}

		$this->ManifestPDF()->save($Asset);
		$this->load('ManifestPDF');

		return $this;
	}

	public function createAssetArchive() {
		return AssetArchive::createFromPickupDate($this);
	}

	public function verifyAssetsDownloadable() {
		if ( ! $this->LabelsPdf) {
			return false;
		}

		if ( ! $this->ManifestPdf) {
			return false;
		}

		if (false == file_exists(storage_path($this->LabelsPdf->filename))) {
			return false;
		}

		if (false == file_exists(storage_path($this->ManifestPdf->filename))) {
			return false;
		}

		return true;
	}

	public function removeFulfillmentBatches() {
		return Fulfillment::destroy($this->Fulfillment->pluck('id'));
	}

	public static function getPickupDatesWithPendingFulfillments() {
		return static::select('pickup_date.*')
					->from('pickup_date')
					->join('order', 'order.pickup_date_id', '=', 'pickup_date.id')
					->join('fulfillment_order', 'fulfillment_order.order_id', '=', 'order.id', 'left')
					->whereNull('fulfillment_order.fulfillment_id')
					->where('order.order_status', Order::STATUS_PENDING_PICKUP)
					->orderBy('pickup_date.pickup_date', 'ASC')
					->groupBy('pickup_date.id')
					->get();
	}

	public static function withFulfillmentsAfter(Carbon $Start = null) {
		$Start = $Start ?: carbon();

		return static::with('Fulfillment', 'Fulfillment.Order')
				->select('pickup_date.*')
				// ->where(DB::raw('DATE(pickup_date)'), '>=', $Start->format('Y-m-d'))
				->join('fulfillment', 'fulfillment.pickup_date_id', '=', 'pickup_date.id')
				->groupBy('pickup_date.id')
				->orderBy('pickup_date.pickup_date', 'ASC')
				->get();
	}

	public static function getAvailableDates() {
		return static::where('pickup_date', '>=', DB::raw('NOW() + INTERVAL ' . config('hsdb.pickup-date-lead-time-hours') . ' HOUR'))
					->whereNull('reconciled_at')
					->orderBy('pickup_date', 'ASC')
					->get();
	}

	public function toArray() {
		$array = parent::toArray();
		$array['pickup_date'] = $this->pickup_date->format('Y-m-d H:i:s');

		return $array;
	}
}
