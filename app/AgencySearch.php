<?php namespace App;

use DB;

class AgencySearch {
	protected $search;
	protected $status;

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

	public function perPage($num) {
		$num = max(min(100, $num), 25);
		$this->per_page = $num;
		return $this;
	}

	public function page($num) {
		$num = max(1, $num);
		$this->page = $num;
	}

	public function get() {
		if ( ! $this->Paginator) {
			$this->buildQuery();
			$this->Paginator = $this->Query->paginate($this->perPage, ['a.id'], 'page', $this->page);
		}

		return $this->getAgenciesById($this->agencyIds());
	}

	public function paginate() {
		return $this->Paginator;
	}

	protected function newQuery() {
		return DB::table('agency as a')
				->select(['a.id as agency_id'])
				->join('agency_contact as ac', 'ac.agency_id', '=', 'a.id', 'left')
				->join('contact', 'contact.id', '=', 'ac.contact_id', 'left')
				->join('users as u', 'u.agency_id', '=', 'a.id')
				->join('child', 'child.agency_id', '=', 'a.id', 'left')
				->join('guardian as g', 'g.agency_id', '=', 'a.id', 'left')
				->orderBy('a.name', 'ASC')
				->where('a.flag_visible', '=', 1)
				->groupBy('a.id');
	}

	protected function buildQuery() {
		$this->Query = $this->newQuery();

		if ($this->search) {
			$search_query = "%{$this->search}%";
			$this->Query->where(function ($Query) use ($search_query) {
				$Query->where('a.id', 'like', $search_query)
					// $Query->where('a.name', 'like', $search_query)
					->orWhere('a.name', 'like', $search_query)
					->orWhere('a.id_prefix', 'like', $search_query);
				// ->orWhere('u.username', 'like', $search_query)
				// ->orWhere('child.name', 'like', $search_query)
				// ->orWhere('child.uniq_id', 'like', $search_query)
				// ->orWhere('contact.name', 'like', $search_query)
				// ->orWhere('contact.phone', 'like', $search_query)
				// ->orWhere('contact.email', 'like', $search_query)
				// ->orWhere('g.name', 'like', $search_query);
			});
		}
	    if ($this->status) {
			$this->Query->where('a.agency_status', '=', $this->status);
		}
		return $this;
	}

	protected function agencyIds() {
		return $this->Paginator->pluck('agency_id')->all();
	}

	protected function getAgenciesById(array $ids) {
		return Agency::with(['Contact', 'User'])
						->whereIn('id', $ids)
						->orderBy('name', 'ASC')
						->get();
	}
}
