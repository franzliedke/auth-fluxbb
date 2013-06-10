<?php namespace FranzLiedke\AuthFluxBB;

use Illuminate\Auth\UserInterface;
use Illuminate\Database\Connection;

class User implements UserInterface {

	/**
	 * The user properties as stored in the database.
	 *
	 * @var array
	 */
	protected $columns;

	/**
	 * Create a new FluxBB user.
	 *
	 * @param  array  $columns
	 * @return void
	 */
	public function __construct(array $columns)
	{
		$this->columns = $columns;
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->columns['username'];
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->columns['password'];
	}

}
