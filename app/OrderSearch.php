<?php namespace App;

use App\Agency;
use App\Queries\OrderProductSummaryQuery;
use App\Queries\OrderStatusSummaryQuery;
use Carbon\Carbon;
use DB;

class OrderSearch {
	protected $search;
	protected $status;
	protected $startDate;
	protected $endDate;
	protected $Agency;
	protected $orderBy = [];

	protected $perPage = 25;
	protected $page = 1;

	protected $Query;
	protected $Paginator;

	public function search($query = null) {
		$this->search = $query;
		return $this;
	}

	public function status($status = null) {
		$this->status = $status;
		return $this;
	}

	public function startDate($date) {
		$this->startDate = new Carbon($date);
		return $this;
	}

	public function endDate($date) {
		$this->endDate = new Carbon($date);
		return $this;
	}

	public function perPage($num) {
		$num = max(min(100, $num), 25);
		$this->per_page = $num;
		return $this;
	}

	public function page($num) {
		$num = max(1, $num);
		$this->page = $num;
	}

	public function agency(Agency $Agency) {
		$this->Agency = $Agency;
	}

	public function orderBy($field, $direction = 'ASC') {
		if ($field === null) {
			// clearing sort rules
			$this->orderBy = [];
		} else {
			$this->orderBy []= [ $field, $direction ];
		}

		return $this;
	}

	public function get() {
		if (!$this->Paginator) {
			$this->buildQuery();
			// dd($this->Query->toSql(), $this->Query->getBindings());
			$this->Paginator = $this->Query->paginate($this->perPage, ['order_id'], 'page', $this->page);
		}

		return $this->getOrdersById($this->orderIds());
	}

	public function paginate() {
		return $this->Paginator;
	}

	public function getSummary() {
		$this->buildQuery();
		return OrderStatusSummaryQuery::create()->whereIdIn(
			$this->Query->get()->pluck('order_id')->all()
		)->getSummary();
	}

	public function getProductSummary() {
		$this->buildQuery();
		return OrderProductSummaryQuery::create()->orderIds(
			$this->Query->get()->pluck('order_id')->all()
		)->getSummary();
	}

	protected function newQuery() {
		return DB::table('order as o')
				->select(['o.id as order_id'])
				->join('agency as a', 'a.id', '=', 'o.agency_id')
				->join('agency_contact as ac', 'ac.agency_id', '=', 'a.id', 'left')
				->join('contact', 'contact.id', '=', 'ac.contact_id', 'left')
				->join('users as u', 'u.agency_id', '=', 'a.id')
				->join('order_child as oc', 'oc.order_id', '=', 'o.id', 'left')
				->join('child', 'child.id', '=', 'oc.child_id', 'left')
				->join('pickup_date as d', 'd.id', '=', 'o.pickup_date_id', 'left')
				->join('guardian as g', 'g.id', '=', 'child.guardian_id', 'left')
				->orderBy('o.created_at', 'DESC')
				->orderBy('o.id', 'DESC')
				->groupBy('o.id');
	}

	protected function buildQuery() {
		$this->Query = $this->newQuery();

		if ($this->search) {
			$search_query = "%{$this->search}%";
			$this->Query->where(function($Query) use ($search_query) {
				$Query->where('o.id', 'like', $search_query)
					->orWhere(DB::raw('CONCAT(a.id_prefix, "-", o.id)'), 'like', $search_query)
					->orWhere(DB::raw('CONCAT(a.id_prefix, "-", DATE_FORMAT(o.created_at, "%y%m%d"), "-", o.id)'), 'like', $search_query)
					->orWhere('a.name', 'like', $search_query)
					->orWhere('a.id_prefix', 'like', $search_query)
					->orWhere('u.username', 'like', $search_query)
					->orWhere('child.name', 'like', $search_query)
					->orWhere('child.uniq_id', 'like', $search_query)
					->orWhere('contact.name', 'like', $search_query)
					->orWhere('contact.phone', 'like', $search_query)
					->orWhere('contact.email', 'like', $search_query)
					->orWhere('g.name', 'like', $search_query);
			});
		}

		if ($this->status) {
			$this->Query->where('o.order_status', '=', $this->status);
		}

		if ($this->startDate) {
			$this->Query->where('o.created_at', '>=', (new Carbon($this->startDate))->format('Y-m-d 00:00:00'));
		}

		if ($this->endDate) {
			$this->Query->where('o.created_at', '<=', (new Carbon($this->endDate))->format('Y-m-d 23:59:59'));
		}

		if ($this->Agency) {
			$this->Query->where('a.id', '=', $this->Agency->id);
		}

		return $this;
	}

	protected function orderIds() {
		return $this->Paginator->pluck('order_id')->all();
	}

	protected function getOrdersById(array $ids){
		$Query = Order::with(['PickupDate', 'Agency', 'Child', 'Child.Location', 'Item', 'Item.Product', 'Item.Product.Category'])
					->join('pickup_date', 'pickup_date.id', '=', 'order.pickup_date_id', 'LEFT')
					->whereIn('order.id', $ids);

		if (!count($this->orderBy)) {
			$Query->orderBy('order.created_at', 'ASC');
		} else {
			collect($this->orderBy)->map(function($order) use ($Query) {
				[ $field, $direction ] = $order;

				if (strstr($field, '.') === false) {
					$field = "order.{$field}";
				}

				$Query->orderBy($field, $direction);
			});
		}

		return $Query->get(['order.*']);
	}
}