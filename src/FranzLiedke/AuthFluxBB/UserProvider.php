<?php namespace FranzLiedke\AuthFluxBB;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserProviderInterface;
use Illuminate\Database\Connection;

class UserProvider implements UserProviderInterface {

	/**
	 * The active database connection.
	 *
	 * @param  \Illuminate\Database\Connection
	 */
	protected $database;

	/**
	 * Create a new database user provider.
	 *
	 * @param  \Illuminate\Database\Connection  $database
	 * @return void
	 */
	public function __construct(Connection $database)
	{
		$this->database = $database;
	}

	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed  $identifier
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveByID($identifier)
	{
		$result = $this->newQuery()->find($identifier);

		if ( ! is_null($result))
		{
			return new User((array) $result);
		}
	}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveByCredentials(array $credentials)
	{
		$query = $this->newQuery();

		foreach ($credentials as $key => $value)
		{
			if ( ! str_contains($key, 'password')) $query->where($key, $value);
		}

		$result = $query->first();

		if ( ! is_null($result))
		{
			return new User((array) $result);
		}
	}

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validateCredentials(UserInterface $user, array $credentials)
	{
		$hash = sha1($credentials['password']);

		return $hash == $user->getAuthPassword();
	}

	/**
	 * Return the name of the users table.
	 * 
	 * @return Illuminate\Database\Query\Builder
	 */
	protected function newQuery()
	{
		return $this->database->table('users');
	}

}
