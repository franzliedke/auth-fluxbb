<?php namespace FranzLiedke\AuthFluxBB;

use Illuminate\Database\Connection;

class ConfigReader {

	/**
	 * The database connection instance for loading the config data.
	 *
	 * @var \Illuminate\Database\Connection
	 */
	protected $database;

	/**
	 * The path where the cached config is stored.
	 *
	 * @var string
	 */
	protected $cachePath;

	/**
	 * All config settings.
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Create a new config loader.
	 *
	 * @param  \Illuminate\Database\Connection  $database
	 * @param  string  $cachePath
	 * @return void
	 */
	public function __construct(Connection $database, $cachePath)
	{
		$this->database = $database;
		$this->cachePath = $cachePath;
	}

	/**
	 * Get the value of the config setting with the given key.
	 *
	 * @param  string  $key
	 * @return string
	 */
	public function get($key)
	{
		$settings = $this->settings();

		return array_get($settings, $key, '');
	}

	/**
	 * Load and return all config settings.
	 *
	 * @return array
	 */
	protected function settings()
	{
		if ( ! isset($this->settings))
		{
			if ($this->cacheFileExists())
			{
				$this->settings = $this->loadFromCache();
			}
			else
			{
				$this->settings = $this->loadFromDatabase();
			}
		}

		return $this->settings;
	}

	/**
	 * Detect whether the config cache file has been created.
	 *
	 * @return bool
	 */
	protected function cacheFileExists()
	{
		return file_exists($this->cachePath . '/cache_config.php');
	}

	/**
	 * Load the config from the cache file.
	 *
	 * @return array
	 */
	protected function loadFromCache()
	{
		include $this->cachePath . '/cache_config.php';

		return $pun_config;
	}

	/**
	 * Load the config from the database.
	 *
	 * @return array
	 */
	protected function loadFromDatabase()
	{
		return $this->database->table('config')->lists('conf_value', 'conf_name');
	}

}
