<?php namespace App;

use App\AssetArchive;
use App\Jobs\GenerateLabels;
use App\Jobs\GenerateManifest;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Fulfillment extends Model {
	public static $filledOrdersByPickupDate;
	public static $unexportedOrdersByPickupDate;

	protected $connection = null;
	protected $table = 'fulfillment';
	public $timestamps = true;
	protected $guarded = [];

	public function Order() {
		return $this->belongsToMany(Order::class, 'fulfillment_order', 'fulfillment_id', 'order_id', 'Order')
					->whereIn('order.order_status', [ Order::STATUS_PENDING_PICKUP, Order::STATUS_FULFILLED ])
					->withPivot('order_status')
					->withTimestamps();
	}

	public function PickupDate() {
		return $this->belongsTo(PickupDate::class, 'pickup_date_id', 'id', 'PickupDate');
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

	public function generatePdfs() {
		$this->generateLabels();
		$this->generateManifest();

		$this->PickupDate->generatePdfs();
		return $this;
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
		return $this->PickupDate->pickup_date->format("Y/m-d") . "/batch-" . $this->id . "/" . carbon()->format("Ymd.His") . '.manifest.' . uniqid("", true) . ".pdf";
	}

	public function generatePackingLabelFilename() {
		return $this->PickupDate->pickup_date->format("Y/m-d") . "/batch-" . $this->id . "/" . carbon()->format("Ymd.His") . '.labels.' . uniqid("", true) . ".pdf";
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
		return AssetArchive::createFromBatch($this);
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

	static public function createFromDate(PickupDate $Date) {
		$Orders = static::getUnexportedOrders($Date);

		if ( ! $Orders->count()) {
			throw new Exception("Can not create a fulfillment batch with zero pending orders.");
		}

		return DB::transaction(function() use ($Date, $Orders) {
			$Batch = static::create([
				'pickup_date_id' => $Date->id,
			]);

			$Batch->Order()->sync( $Orders->pluck('id')->all() );

			return $Batch;
		});
	}

	static public function getUnexportedOrders(PickupDate $PickupDate = null) {
		if (!$PickupDate) {
			return collect([]);
		}

		static::cachedUnexportedOrdersByPickupDate();
		return static::getUnexportedOrdersForPickupDate($PickupDate);
	}

	static public function cachedUnexportedOrdersByPickupDate() {
		if ( null !== static::$unexportedOrdersByPickupDate) {
			return;
		}

		static::$unexportedOrdersByPickupDate = 
				Order::with('PickupDate', 'Child', 'Child.Child', 'Agency', 'Child.Location', 'Item', 'Item.Product', 'Item.Product.Category')
				->join('fulfillment_order', 'order.id', '=', 'fulfillment_order.order_id', 'left')
				->whereNull('fulfillment_order.fulfillment_id')
				->where('order.order_status', '=', Order::STATUS_PENDING_PICKUP)
				// ->where('order.pickup_date_id', '=', $PickupDate->id)
				->orderBy('order.created_at', 'ASC')
				->get(['order.*'])
				->reduce(function($carry, $order) {
					if ( !isset($carry[ $order->PickupDate->id ])) {
						$carry[ $order->PickupDate->id ] = [];
					}

					$carry[ $order->PickupDate->id ] []= $order;

					return $carry;
				}, []);;
	}

	static public function getUnexportedOrdersForPickupDate(PickupDate $date) {
		if (isset(static::$unexportedOrdersByPickupDate[ $date->id ]))
		{
			return collect(static::$unexportedOrdersByPickupDate[ $date->id ]);
		}

		return collect();	
	}


	static public function getFilledOrders(PickupDate $PickupDate = null) {
		if (!$PickupDate) {
			return collect([]);
		}

		static::cacheFilledOrdersByPickupDate();
		return static::getFilledOrdersForPickupDate($PickupDate);
	}

	static public function cacheFilledOrdersByPickupDate() {
		if ( null !== static::$filledOrdersByPickupDate) {
			return;
		}

		static::$filledOrdersByPickupDate = 
				Order::with('PickupDate', 'Child', 'Child.Child', 'Agency', 'Child.Location', 'Item', 'Item.Product', 'Item.Product.Category')
				->join('fulfillment_order', 'order.id', '=', 'fulfillment_order.order_id', 'left')
				->whereNotNull('fulfillment_order.fulfillment_id')
				->where('order.order_status', '=', Order::STATUS_PENDING_PICKUP)
				// ->where('order.pickup_date_id', '=', $PickupDate->id)
				->orderBy('order.created_at', 'ASC')
				->get()
				->reduce(function($carry, $order) {
					if ( !isset($carry[ $order->PickupDate->id ])) {
						$carry[ $order->PickupDate->id ] = [];
					}

					$carry[ $order->PickupDate->id ] []= $order;

					return $carry;
				}, []);
	}

	static public function getFilledOrdersForPickupDate(PickupDate $date) {
		if (isset(static::$filledOrdersByPickupDate[ $date->id ]))
		{
			return collect(static::$filledOrdersByPickupDate[ $date->id ]);
		}

		return collect();
	}

	// static public function getFulfillmentsAfter(Carbon $Start = null) {
	// 	$Start = $Start ?: carbon();

	// 	return static::with('Order', 'PickupDate')
	// 			->join('pickup_date', 'pickup_date.id', '=', 'fulfillment.pickup_date_id')
	// 			->where(DB::raw('DATE(pickup_date.pickup_date)'), '>=', $Stop->format('Y-m-d'))
	// 			->get();
	// }
}
