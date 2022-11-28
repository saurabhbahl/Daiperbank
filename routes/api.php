<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'orders'], function () {
	Route::post('create')->uses('Api\Order\CreateController@post');

	Route::group(['prefix' => '{order_id}'], function () {
		Route::post('/child')->uses('Api\Order\AddOrderChildController@post');

		Route::post('/child/{order_child_id}')->uses('Api\Order\OrderChildController@post');
		Route::delete('/child/{order_child_id}')->uses('Api\Order\OrderChildController@delete');
		Route::post('/child/{order_child_id}/restore')->uses('Api\Order\OrderChildController@restore');

		Route::post('/child/{order_child_id}/approve')->uses('Api\Order\OrderChildApprovalController@post');
		Route::delete('/child/{order_child_id}/approve')->uses('Api\Order\OrderChildApprovalController@delete');

		Route::group(['prefix' => 'notes'], function () {
			Route::post('/')->uses('Api\Order\NoteController@post');
			Route::delete('/{note_id}')->uses('Api\Order\NoteController@delete');
		});
	});
});

Route::group(['prefix' => 'pickup-dates', 'middleware' => ['admin']], function () {
	Route::group(['prefix' => '{pickup_date_id}'], function () {
		Route::get('/orders')->uses('Api\PickupDate\OrdersController@get');
		Route::post('/reschedule')->uses('Api\PickupDate\RescheduleController@post');
		Route::post('/reconcile')->uses('Api\PickupDate\ReconcileController@post');
		Route::delete('/')->uses('Api\PickupDate\DeleteController@delete');
	});

	Route::post('/')->uses('Api\PickupDate\CreateController@post');
});

Route::group(['prefix' => 'child'], function () {
	Route::get('{child_id}')->uses('Api\Child\ViewController@get');
	Route::post('{child_id?}')->uses('Api\Child\ViewController@post');
	Route::delete('{child_id}')->uses('Api\Child\ViewController@delete');
});

Route::group(['prefix' => 'guardian'], function () {
	Route::get('/')->uses('Api\Guardian\IndexController@get');
});

Route::group(['prefix' => 'notifications'], function () {
	Route::post('/read')->uses('Api\Notifications\ReadController@post');
});

Route::group(['prefix' => 'agency'], function () {
	Route::get('/')->uses('Api\Agency\IndexController@get');
	Route::post('/{agency_id}/note')->uses('Api\Agency\NoteController@post');
});

Route::group(['prefix' => 'product'], function () {
	Route::get('/')->uses('Api\Product\IndexController@get');
});
