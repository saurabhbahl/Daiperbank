<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model {
	protected $connection = null;
	protected $table = 'zipcode';
	public $timestamps = false;
	protected $guarded = [];
}