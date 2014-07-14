<?php namespace FranzLiedke\AuthFluxBB;

use Illuminate\Auth\Reminders\ReminderServiceProvider;
use Illuminate\Support\ServiceProvider;

class AuthFluxBBServiceProvider extends ReminderServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		parent::register();
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

	/**
	 * Register the reminder repository implementation.
	 *
	 * @return void
	 */
	protected function registerReminderRepository()
	{
		$this->app->bindShared('auth.reminder.repository', function($app)
		{
			$connection = $app['db']->connection();

			return new FluxReminderRepository($connection);
		});
	}
}
