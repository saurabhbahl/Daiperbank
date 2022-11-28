<?php namespace App\Scopes;

use App\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CancelledScope implements Scope {
	protected $extensions = ['WithCancelled', 'WithoutCancelled', 'OnlyCancelled'];

	public function apply(Builder $Builder, Model $Model) {
		$Builder->where('order_status', '!=', Order::STATUS_CANCELLED);
	}

	public function extend(Builder $Builder) {
		foreach ($this->extensions as $extension) {
			$this->{"add{$extension}"}($Builder);
		}
	}

	public function addWithCancelled(Builder $Builder) {
		$Builder->macro('withTrashed', function(Builder $Builder) {
			return $Builder->withoutGlobalScope($this);
		});
	}

	public function addWithoutCancelled(Builder $Builder) {
		$Builder->macro('withoutCancelled', function(Builder $Builder) {
			$Builder->withoutGlobalScope($this)->where('order_status', '!=', Order::STATUS_CANCELLED);

			return $Builder;
		});
	}

	public function addOnlyCancelled(Builder $Builder) {
		$Builder->macro('onlyCancelled', function(Builder $Builder) {
			$Builder->withoutGlobalScope($this)->where('order_status', '=', Order::STATUS_CANCELLED);

			return $Builder;
		});
	}
}