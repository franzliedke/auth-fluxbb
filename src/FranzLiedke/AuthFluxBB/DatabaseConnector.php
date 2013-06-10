<?php namespace FranzLiedke\AuthFluxBB;

use Illuminate\Database\Connectors\ConnectionFactory;

class DatabaseConnector {

	/**
	 * The database connection factory.
	 *
	 * @var \Illuminate\Database\Connectors\ConnectionFactory
	 */
	protected $factory;

	/**
	 * Create a new database connector.
	 *
	 * @param  \Illuminate\Database\Connectors\ConnectionFactory  $factory
	 * @param  \FranzLiedke\AuthFluxBB\ConfigParser  $parser
	 * @return void
	 */
	public function __construct(ConnectionFactory $factory, ConfigParser $parser)
	{
		$this->factory = $factory;
		$this->parser = $parser;
	}

	/**
	 * Get a database connection as configured.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function connection()
	{
		$config = array(
			'driver'    => $this->getDatabaseDriver(),
			'host'      => $this->parser->get('db_host'),
			'database'  => $this->parser->get('db_name'),
			'username'  => $this->parser->get('db_username'),
			'password'  => $this->parser->get('db_password'),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => $this->parser->get('db_prefix'),
		);

		return $this->factory->make($config, 'auth-fluxbb');
	}

	/**
	 * Return the name of the database driver to be used.
	 * 
	 * @return string
	 */
	protected function getDatabaseDriver()
	{
		$type = $this->parser->get('db_type');

		// FluxBB has multiple different database adapters for MySQL
		if (str_contains($type, 'mysql')) return 'mysql';

		return $type;
	}

}
