<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::auth();

Route::group(['middleware' => ['auth']], function () {
	Route::get('/', function () { return redirect()->route('home'); });
	Route::get('/home', 'HomeController@index')->name('home');
	Route::post('/home', 'HomeController@post');

	// user things
	Route::group([], function () {
		Route::group(['prefix' => 'order'], function () {
			Route::name('order.index')->get('/')->uses('Order\IndexController@get');

			Route::name('order.create')->get('/{order_id}/create')->uses('Order\CreateController@get');
			Route::name('order.create')->post('/{order_id}/create')->uses('Order\CreateController@post');

			Route::name('order.view')->get('{order_id}')->uses('Order\ViewController@get');
			Route::post('{order_id}')->uses('Order\ViewController@post');

			Route::name('order.print')->get('{order_id}/print')->uses('Order\PrintController@get');
		});

		Route::group(['prefix' => 'family'], function () {
			Route::name('family.index')->get('/')->uses('Family\IndexController@get');
		});
		Route::group(['prefix' => 'menstruator'], function () {
			Route::name('menstruator.index')->get('/')->uses('Menstruator\IndexController@get');
		});
		Route::group(['prefix' => 'notifications'], function () {
			Route::name('notifications.index')->get('/')->uses('Notification\IndexController@get');
			Route::post('/')->uses('Notification\IndexController@post');
		});
	});

	// administrator things
	Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
		Route::group(['prefix' => 'agency'], function () {
			Route::name('admin.agency.index')->get('/')->uses('Admin\Agency\IndexController@get');
			Route::post('/create')->uses('Admin\Agency\UpdateController@post');

			// Route::name('admin.agency.view')->get('{agency_id}')->uses('Admin\Agency\ViewController@get');
			Route::name('admin.agency.create')->get('create')->uses('Admin\Agency\UpdateController@get');
			Route::name('admin.agency.edit')->get('{agency_id}')->uses('Admin\Agency\UpdateController@get');
			Route::post('{agency_id}')->uses('Admin\Agency\UpdateController@post');

			Route::name('admin.agency.status')->post('{agency_id}/status')->uses('Admin\Agency\StatusController@post');
			Route::name('admin.agency.act_as')->post('{agency_id}/act-as')->uses('Admin\Agency\ActingAsController@post');
		});

		Route::group(['prefix' => 'inventory'], function () {
			Route::name('admin.inventory.index')->get('/')->uses('Admin\Inventory\IndexController@get');
			Route::name('admin.inventory.create_adjustment')->get('/create')->uses('Admin\Inventory\CreateController@get');
			Route::post('/create')->uses('Admin\Inventory\CreateController@post');
		});

		Route::group(['prefix' => 'order'], function () {
			Route::name('admin.order.index')->get('/')->uses('Admin\Order\IndexController@get');
			Route::name('admin.order.view')->get('/{order_id}')->uses('Admin\Order\ViewController@get');
			Route::post('/{order_id}')->uses('Admin\Order\ViewController@post');
		});

		Route::group(['prefix' => 'pick-up'], function () {
			Route::name('admin.pickup.index')->get('/')->uses('Admin\PickupDate\IndexController@get');
		});

		Route::group(['prefix' => 'fulfillment'], function () {
			Route::name('admin.fulfillment.index')->get('/')->uses('Admin\Fulfillment\IndexController@get');
			Route::name('admin.fulfillment.index')->post('/')->uses('Admin\Fulfillment\IndexController@post');
			Route::name('admin.fulfillment.exported')->get('/upcoming')->uses('Admin\Fulfillment\ExportedController@get');

			Route::get('/manifest-preview')->uses('Admin\Fulfillment\ManifestController@get');

			Route::name('batch.asset-download')->get('/batch/{batch_id}/download/all')->uses('Admin\Fulfillment\AssetController@batch_download_all');
			Route::name('pickup-date.asset-download')->get('/pickup-date/{pickup_date_id}/download/all')->uses('Admin\Fulfillment\AssetController@pickupdate_download_all');
		});

		Route::group(['prefix' => 'reporting'], function () {
			Route::name('admin.reporting')->get('/')->uses('Admin\Reporting\ReportController@get');
			Route::post('/')->uses('Admin\Reporting\ReportController@post');
		});
	});
});
