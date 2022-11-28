<?php

namespace App\Providers;

use App\Providers\SessionNoPassGuard;
use Auth;
use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\CreatesUserProviders;
use App\Notifications\DatabaseNotificationCollection;

class AppServiceProvider extends ServiceProvider {

	use CreatesUserProviders;

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		// app('Debugbar')->enable();
		view()->composer('*', function($view) {
			$Notifications = null;
			if (Auth()->User()) {
				$Notifications = Auth()->User()->Agency->notifications()->take(config('hsdb.notifications.widget_limit', 10))->get(['*']);
			}

			$view->with('Notifications', $Notifications);
		});

		if ($this->app->environment('local')) {
			$this->registerNoPasswordAuthProvider();
		}
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		if ($this->app->environment('local', 'testing')) {
			$this->app->register(DuskServiceProvider::class);
		}

		if ($this->app->environment('local')) {
			$this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
			$this->app->alias('Debugbar', \Barryvdh\Debugbar\Facade::class);
		}
	}

	public function registerNoPasswordAuthProvider() {
		Auth::extend('session.noPassword', function($app, $name, $config) {
			$provider = $this->createUserProvider($config['provider']);

			return new SessionNoPassGuard($name, $provider, $this->app['session.store']);
		});
	}
}
