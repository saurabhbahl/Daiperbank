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

		Route::group(['prefix' => 'notifications'], function () {
			Route::name('notifications.index')->get('/')->uses('Notification\IndexController@get');
			Route::post('/')->uses('Notification\IndexController@post');
		});
		//Menstruator
		Route::group(['prefix' => 'menstruator'], function () {
			Route::name('menstruator.index')->get('/')->uses('Menstruator\IndexController@get');
		});
		// Partner Handbook
		Route::group(['prefix' => 'partner-handbook'], function () {
			Route::name('partner-handbook.index')->get('/')->uses('PartnerHandbook\IndexController@get');
		});

		// Additional Resources
		Route::group(['prefix' => 'additional-resources'], function () {
			Route::name('additional-resources.index')->get('/')->uses('AdditionalResources\IndexController@get');
		});

		// Agency Profile
		Route::group(['prefix' => 'profile'], function () {
			Route::name('agency.profile.index')->get('/')->uses('Agency\ProfileController@index');
		});

		// Partner Agreement
		Route::group(['prefix' => 'agreement'], function () {
			Route::name('agreement.index')->get('/')->uses('Agreement\IndexController@get');
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
            
			// Agency Profile
			Route::name('admin.agency.profile')->get('profile/{agency_id}')->uses('Admin\Agency\ProfileController@get');
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

		Route::group(['prefix' => 'resource'], function () {
			Route::name('admin.resource.create')->get('create')->uses('Admin\Resource\ResourceController@index');
			Route::name('admin.resource.create')->post('create')->uses('Admin\Resource\ResourceController@create');
			Route::name('admin.resource.create')->get('create')->uses('Admin\Resource\ResourceController@show');
			Route::name('admin.resource.edit')->get('edit/{resource_id}')->uses('Admin\Resource\ResourceController@edit');
			Route::name('admin.resource.update')->put('{resource_id}')->uses('Admin\Resource\ResourceController@update');
			Route::name('admin.resource.destroy')->get('delete/{resource_id}')->uses('Admin\Resource\ResourceController@destroy');
	    });

		Route::group(['prefix' => 'additionalresource'], function () {
			Route::name('admin.additionalresource.create')->get('create')->uses('Admin\AdditionalResources\additionalresources@index');
			Route::name('admin.additionalresource.create')->post('create')->uses('Admin\AdditionalResources\additionalresources@create');
			Route::name('admin.additionalresource.create')->get('create')->uses('Admin\AdditionalResources\additionalresources@show');
			Route::name('admin.additionalresource.edit')->get('edit/{resource_id}')->uses('Admin\AdditionalResources\additionalresources@edit');
			Route::name('admin.additionalresource.update')->put('{resource_id}')->uses('Admin\AdditionalResources\additionalresources@update');
			Route::name('admin.additionalresource.destroy')->get('delete/{resource_id}')->uses('Admin\AdditionalResources\additionalresources@destroy');
	    });
		
		Route::group(['prefix' => 'agreement'], function () {
			Route::name('admin.agreement.create')->get('create')->uses('Admin\Agreement\AgreementController@index');
			Route::name('admin.agreement.create')->post('create')->uses('Admin\Agreement\AgreementController@create');
			Route::name('admin.agreement.create')->get('create')->uses('Admin\Agreement\AgreementController@show');
			Route::name('admin.agreement.edit')->get('edit/{agreement_id}')->uses('Admin\Agreement\AgreementController@edit');
			Route::name('admin.agreement.update')->put('{agreement_id}')->uses('Admin\Agreement\AgreementController@update');
			Route::name('admin.agreement.destroy')->get('delete/{agreement_id}')->uses('Admin\Agreement\AgreementController@destroy');
	    });

		Route::group(['prefix' => 'settings'], function () {
			Route::name('admin.settings.index')->get('/')->uses('Admin\Settings\SettingController@index');
			Route::name('admin.settings.index')->post('/')->uses('Admin\Settings\SettingController@store');
	    });

	});
});