<?php namespace App\Collections;

use App\Jobs\GenerateLabels;
use Illuminate\Database\Eloquent\Collection;

class PickupDates extends Collection {
	public function findFor($pickup_date) {
		return $this->filter(function($Date) use ($pickup_date) {
			return $Date->pickup_date->format('Y-m-d') == $pickup_date;
		})->first();
	}

	public function findFirstAfter($pickup_date) {
		$pickup_date = carbon($pickup_date);
		return $this->sortBy(function($Date) {
			return $Date->pickup_date->format('Y-m-d');
		}, SORT_STRING, false)->filter(function($Date) use ($pickup_date) {
			return $Date->pickup_date->gte($pickup_date);
		})->first();
	}
}