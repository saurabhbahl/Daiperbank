<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Zip extends Model {
	protected $connection = null;
	protected $table = 'zipcode';
	public $timestamps = true;
	protected $guarded = [];
	protected $primaryKey = 'zip';
	public $incrementing = false;
}