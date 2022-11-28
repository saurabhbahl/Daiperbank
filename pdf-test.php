<?php

require './vendor/autoload.php';

use mikehaertl\pdftk\Pdf;

$Pdf = new Pdf('/Users/jrubenstein/Sites/personal/hsdb/resources/assets/pdf/Avery.18160.Child.Label.Form.pdf');

$fields = [];

for ($lbl = 0; $lbl < 30; $lbl++) {
	$fields = $fields + [
		"lbl{$lbl}.order_number" => "lbl {$lbl} order",
		"lbl{$lbl}.agency_name" => "lbl {$lbl} agency",
		"lbl{$lbl}.child_name" => "lbl {$lbl} child",
		"lbl{$lbl}.item_name" => "lbl {$lbl} item",
		"lbl{$lbl}.item_qty" => "lbl{$lbl}"
	];
}

$Pdf->fillForm($fields);



// $Pdf->fillForm( [
// 	'lbl1_order_number' => '#' . rand(10000, 99999) . '-' . rand(100, 9999),
// 	'lbl1_agency_name' => 'Knopeleski Ltd York PA',

// 	'lbl1_product.0.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.0.qty' => '9999',

// 	'lbl1_product.1.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.1.qty' => '9999',

// 	'lbl1_product.2.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.2.qty' => '9999',

// 	'lbl1_product.3.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.3.qty' => '9999',

// 	'lbl1_product.4.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.4.qty' => '9999',

// 	'lbl1_product.5.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.5.qty' => '9999',

// 	'lbl1_product.6.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.6.qty' => '9999',

// 	'lbl1_product.7.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.7.qty' => '9999',

// 	'lbl1_product.8.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.8.qty' => '9999',

// 	'lbl1_product.9.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.9.qty' => '9999',

// 	'lbl1_product.10.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.10.qty' => '9999',

// 	'lbl1_product.11.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.11.qty' => '9999',

// 	'lbl1_product.12.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.12.qty' => '9999',

// 	'lbl1_product.13.name' => '2T-3T Pullup Boy',
// 	'lbl1_product.13.qty' => '9999',
// ])
// ->needAppearances();

$Pdf->saveAs('filled.pdf');
