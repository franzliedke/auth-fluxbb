<?php namespace FranzLiedke\AuthFluxBB;

use Illuminate\Support\ServiceProvider;

class AuthFluxBBServiceProvider extends ServiceProvider {

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

		// Register the FluxBB authentication driver
		$this->app->resolving('auth', function($auth)
		{
			$auth->extend('fluxbb1', function($app)
			{
				$connector = $app['fluxbb1.db.connector'];
				$provider = new UserProvider($connector->connection());

				return new Guard($provider, $app['fluxbb1.cookie.storage']);
			});
		});

		// Make sure the FluxBB cookie will not be encrypted
		$this->app->resolving('Illuminate\Cookie\Middleware\EncryptCookies', function($middleware)
		{
			$configParser = $this->app->make('fluxbb1.config');
			
			$middleware->disableFor($this->parser->get('cookie_name'));
		});
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('franzliedke/auth-fluxbb');
	}

}
