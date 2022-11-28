<?php

use App\Product;

function get_states() {
	return [
		"AL" => "Alabama",
		"AK" => "Alaska",
		"AZ" => "Arizona",
		"AR" => "Arkansas",
		"CA" => "California",
		"CO" => "Colorado",
		"CT" => "Connecticut",
		"DE" => "Delaware",
		"DC" => "District of Columbia",
		"FL" => "Florida",
		"GA" => "Georgia",
		"HI" => "Hawaii",
		"ID" => "Idaho",
		"IL" => "Illinois",
		"IN" => "Indiana",
		"IA" => "Iowa",
		"KS" => "Kansas",
		"KY" => "Kentucky",
		"LA" => "Louisiana",
		"ME" => "Maine",
		"MD" => "Maryland",
		"MA" => "Massachusetts",
		"MI" => "Michigan",
		"MN" => "Minnesota",
		"MS" => "Mississippi",
		"MO" => "Missouri",
		"MT" => "Montana",
		"NE" => "Nebraska",
		"NV" => "Nevada",
		"NH" => "New Hampshire",
		"NJ" => "New Jersey",
		"NM" => "New Mexico",
		"NY" => "New York",
		"NC" => "North Carolina",
		"ND" => "North Dakota",
		"OH" => "Ohio",
		"OK" => "Oklahoma",
		"OR" => "Oregon",
		"PA" => "Pennsylvania",
		"RI" => "Rhode Island",
		"SC" => "South Carolina",
		"SD" => "South Dakota",
		"TN" => "Tennessee",
		"TX" => "Texas",
		"UT" => "Utah",
		"VT" => "Vermont",
		"VA" => "Virginia",
		"WA" => "Washington",
		"WV" => "West Virginia",
		"WI" => "Wisconsin",
		"WY" => "Wyoming",
	];
}

function phone_format($phone) {
	if (strlen($phone) === 10) {
		return "(" . substr($phone, 0, 3) . ")"
		. " "
		. substr($phone, 3, 3)
		. "-"
		. substr($phone, -4);
	}

	return $phone;
}

function carbon(...$args) {
	return new Carbon\Carbon(...$args);
}

function inventoryWarning(Product $Product, $qty) {
	$category_id = $Product->Category->id;

	switch ($category_id) {
		case 1:
			return config('hsdb.low_water.diaper.warning') > $qty;

		case 2:
			return config('hsdb.low_water.pullup.warning') > $qty;
	}

	return false;
}

function inventoryCritical(Product $Product, $qty) {
	$category_id = $Product->Category->id;

	switch ($category_id) {
		case 1:
			return config('hsdb.low_water.diaper.critical') > $qty;

		case 2:
			return config('hsdb.low_water.pullup.critical') > $qty;
	}

	return false;
}