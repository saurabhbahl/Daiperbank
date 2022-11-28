<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class MyModel extends Model {
	protected $connection = null;
	protected $table = 'state';
	public $timestamps = false;
	protected $guarded = [];
}