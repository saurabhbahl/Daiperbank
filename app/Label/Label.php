<?php namespace App\Label;

use App\Child;
use App\OrderChild;

class Label {

	protected $orderNumber;
	protected $agency;
	protected $child_name;
	protected $item_name;
	protected $quantity;

	public function __construct($orderNumber, $agency) {
		$this->orderNumber($orderNumber);
		$this->agency($agency);
	}

	public function orderNumber($val = null) {
		if ($val !== null) {
			$this->orderNumber = $val;
			return $this;
		}

		return $this->orderNumber;
	}

	public function agency($val = null) {
		if ($val !== null) {
			$this->agency = $val;
			return $this;
		}

		return $this->agency;
	}

	public function child($val = null) {
		if ($val !== null) {
			$this->child_name = $val;
			return $this;
		}

		return ucwords(strtolower($this->child_name));
	}

	public function product($name = null, $qty = null) {
		if ($name !== null) {
			$this->item_name = $name;
		}

		if ($qty !== null) {
			$this->quantity = $qty;
		}

		if ($name !== null) {
			return $this;
		}

		return $this->item_name;
	}

	public function quantity($qty = null) {
		if ($qty !== null) {
			$this->quantity = $qty;
			return $this;
		}

		return $this->quantity;
	}

	public function addChild(OrderChild $Child) {
		$this->child($Child->name);
		$this->product($Child->Item->Product->full_name, $Child->Item->quantity);

		return $this;
	}

	public function toPdfArray($label_idx) {
		$prefix = $this->labelPrefix($label_idx);
		$p_name = explode(' Period',$this->product());
		$p_name_new= str_replace('Girl ', '',$p_name[0]);
		$p_name_new= str_replace('Boy ', '',$p_name_new);
		$fields = [
			"{$prefix}order_number" => $this->orderNumber(),
			"{$prefix}agency_name" => $this->agency(),
			"{$prefix}child_name" => $this->child(),
			// "{$prefix}item_name" => $this->product(),
			"{$prefix}item_name" => $p_name_new,
			"{$prefix}item_qty" => number_format($this->quantity(), 0),
		];

		return $fields;
	}

	protected function labelPrefix($idx) {
		return "lbl{$idx}.";
	}
}