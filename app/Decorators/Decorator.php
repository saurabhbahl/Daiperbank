<?php namespace App\Decorators;

abstract class Decorator {
	public $Original;

	public function __construct($Original) {
		$this->Original = $Original;
	}

	public static function make($instance) {
		return new static($instance);
	}

	public function __get($prop) {
		return $this->Original->{$prop};
	}

	public function __set($prop, $value) {
		return $this->Original->{$prop} = $value;
	}

	public function __call($method, $params) {
		return $this->Original->{$method}(...$params);
	}

	public function getOriginal() {
		return $this->Original;
	}
}