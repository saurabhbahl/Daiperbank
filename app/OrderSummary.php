<?php namespace App;


use App\Repositories\ProductRepository;

class OrderSummary {
	protected $ProductRepository;
	protected $Order;

	public $children;
	public $diapers;
	public $pullups;

	public $inventory;

	public function __construct(ProductRepository $ProductRepository) {
		$this->ProductRepository = $ProductRepository;
	}

	public static function create(Order $Order) {
		return app(static::class)->setOrder($Order)->createSummary();
	}

	public function setOrder(Order $Order) {
		$this->Order = $Order;

		return $this;
	}

	public function createSummary() {
		$this->summarizeQuantities();
		$this->summarizeInventory();
		$this->summarizeChildren();

		return $this;
	}

	protected function summarizeQuantities() {
		foreach ($this->Order->Item as $Item) {
			if ($Item->Product->Category->id == ProductCategory::CATEGORY_ID_DIAPERS) {
				$this->diapers += $Item->quantity;
			} elseif ($Item->Product->Category->id == ProductCategory::CATEGORY_ID_PULLUPS) {
				$this->pullups += $Item->quantity;
			}
		}

		return $this;
	}

	protected function summarizeInventory() {
		$ordered = $this->getOrderedProductQuantities();
		foreach ($this->ProductRepository->getAllProductsWithInventory() as $Product) {
			$Product->Inventory->ordered = $ordered[$Product->id] ?? 0;
			$this->inventory[$Product->id] = $Product;
		}

		return $this;
	}

	protected function summarizeChildren() {
		$this->children = $this->Order->Child->count();

		return $this;
	}

	protected function getOrderedProductQuantities() {
		$ordered = [];
		foreach ($this->Order->Item as $Item) {
			if ( ! isset($ordered[$Item->product_id])) {
				$ordered[$Item->product_id] = 0;
			}

			$ordered[$Item->product_id] += $Item->quantity;
		}

		return $ordered;
	}
}
