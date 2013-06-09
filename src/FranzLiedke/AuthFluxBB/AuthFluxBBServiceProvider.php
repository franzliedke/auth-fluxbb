<?php namespace FranzLiedke\AuthFluxBB;

use Illuminate\Auth\Guard;
use Illuminate\Support\ServiceProvider;

class AuthFluxBBServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Register the FluxBB authentication driver
		$this->app['auth']->extend('fluxbb', function($app)
		{
			$provider = new UserProvider($app['db']->connection());

			return new Guard($provider, $app['session']);
		});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}