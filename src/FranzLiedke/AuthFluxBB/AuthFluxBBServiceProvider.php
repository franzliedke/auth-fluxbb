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
		$this->package('franzliedke/auth-fluxbb');

		// Register the FluxBB authentication driver
		$this->app['auth']->extend('fluxbb', function($app)
		{
			$connector = $app['fluxbb.db.connector'];

			$provider = new UserProvider($connector->connection());

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
		$this->app['fluxbb.config'] = $this->app->share(function($app)
		{
			$path = $app['config']['auth-fluxbb::fluxbb.path'].'config.php';
			return new ConfigParser($path);
		});

		$this->app['fluxbb.db.connector'] = $this->app->share(function($app)
		{
			$factory = $app['db.factory'];
			$configParser = $app['fluxbb.config'];

			return new DatabaseConnector($factory, $configParser);
		});
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