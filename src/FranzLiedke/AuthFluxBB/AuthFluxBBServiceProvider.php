<?php namespace FranzLiedke\AuthFluxBB;

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
		$this->app['auth']->extend('fluxbb1', function($app)
		{
			$connector = $app['fluxbb1.db.connector'];
			$provider = new UserProvider($connector->connection());

			return new Guard($provider, $app['fluxbb1.cookie.storage']);
		});

		// Once the app has booted, we can include some FluxBB files
		$this->app->booted(function($app)
		{
			if ( ! defined('PUN_ROOT'))
			{
				define('PUN_ROOT', $app['config']['auth-fluxbb::path']);
			}

			include_once PUN_ROOT.'include/functions.php';
		});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['fluxbb1.config'] = $this->app->share(function($app)
		{
			$path = $app['config']['auth-fluxbb::path'].'config.php';

			return new ConfigParser($path);
		});

		$this->app['fluxbb1.db.connector'] = $this->app->share(function($app)
		{
			$factory = $app['db.factory'];
			$configParser = $app['fluxbb1.config'];

			return new DatabaseConnector($factory, $configParser);
		});

		$this->app['fluxbb1.cookie.storage'] = $this->app->share(function($app)
		{
			$configParser = $app['fluxbb1.config'];

			return new CookieStorage($app['request'], $configParser);
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