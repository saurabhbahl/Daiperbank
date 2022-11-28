<?php namespace App;

use DB;

class ChildSearch {
	protected $search;
	protected $guardians;
	protected $agencies;

	protected $withTrashed = false;

	protected $perPage = 25;
	protected $page = 1;

	protected $Query;
	protected $Paginator;

	/**
	 * search string to query for children
	 *
	 * @param  string $query string to query against children and related fields
	 *
	 * @return ChildSearch        $this
	 */
	public function search($query = null) {
		$this->search = $query;
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

	/**
	 * search for children who have one of the specified guardians
	 *
	 * @param  mixed $id pass a single id, or an array, of guardian ids to search for.
	 *
	 * @return ChildSearch	$this
	 */
	public function guardian($id) {
		if ( ! is_array($id)) {
			$id = [ $id ];
		}

		$this->guardians = $id;

		return $this;
	}

	/**
	 * limit search to children who belong to the specified agencies
	 *
	 * @param  mixed $id integer or array of integers referring to the agency ids for which to find children
	 *
	 * @return ChildSearch $this
	 */
	public function agency($id) {
		if ( ! is_array($id)) {
			$id = [ $id ];
		}

		$this->agencies = $id;

		return $this;
	}

	public function get() {
		if ( ! $this->Paginator) {
			$this->buildQuery();
			$this->Paginator = $this->Query->paginate($this->perPage, ['*'], 'page', $this->page);
		}

		return $this->Paginator;
	}

	public function paginate() {
		return $this->Paginator;
	}

	public function withTrashed() {
		$this->withTrashed = true;

		return $this;
	}
	public function getUniqIdAttribute() {
		return $this->Child->uniq_id;
	}
	
	protected function newQuery() {
		return DB::table('child as c')
				->select(['c.*'])
				->join('guardian as g', 'g.id', '=', 'c.guardian_id')
				->join('zipcode as z', 'z.zip', '=', 'c.zip')
				->orderBy('c.name', 'ASC')
				->groupBy('c.id')
				->groupBy('c.uniq_id')
				->groupBy('c.name')
				->groupBy('c.gender')
				->groupBy('c.dob')
				->groupBy('c.zip')
				->groupBy('c.created_at')
				->groupBy('c.updated_at')
				->groupBy('c.deleted_at')
				->groupBy('c.agency_id')
				->groupBy('c.guardian_id');
	}

	protected function buildQuery() {
		$this->Query = $this->newQuery();

		if ($this->search) {
			$search_query = "%{$this->search}%";
			$this->Query->where(function ($Query) use ($search_query) {
				$Query->where('c.name', 'like', $search_query)
					->orWhere('c.uniq_id', 'like', $search_query)
					->orWhere('c.dob', 'like', $search_query)
					->orWhere(DB::raw('DATE_FORMAT(c.dob, "%m/%d/%Y")'), 'like', $search_query)
					->orWhere(DB::raw('DATE_FORMAT(c.dob, "%m-%d-%Y")'), 'like', $search_query)
					->orWhere(DB::raw('DATE_FORMAT(c.dob, "%Y/%m/%d")'), 'like', $search_query)
					->orWhere(DB::raw('DATE_FORMAT(c.dob, "%Y-%m-%d")'), 'like', $search_query)
					->orWhere('c.zip', '=', $search_query)
					->orWhere(DB::raw('CONCAT(z.city, ", ", z.state_abbr)'), 'like', $search_query)
					->orWhere('z.county', 'like', $search_query)
					->orWhere('z.city', 'like', $search_query)
					->orWhere('g.name', 'like', $search_query);
			});
		}

		if ($this->guardians) {
			$this->Query->whereIn('c.guardian_id', $this->guardians);
		}

		if ($this->agencies) {
			$this->Query->whereIn('c.agency_id', $this->agencies);
		}

		if ( ! $this->withTrashed) {
			$this->Query->whereNull('c.deleted_at');
		}

		return $this;
	}
}
