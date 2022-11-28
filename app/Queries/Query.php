<?php namespace App\Queries;

abstract class Query {
	protected $Query;

	abstract public function get();
	static public function create() {
		return new static;
	}
}