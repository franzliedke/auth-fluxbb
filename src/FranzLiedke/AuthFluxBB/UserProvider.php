<?php namespace FranzLiedke\AuthFluxBB;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserProviderInterface;
use Illuminate\Database\Connection;

class UserProvider implements UserProviderInterface {

	/**
	 * The active database connection.
	 *
	 * @var \Illuminate\Database\Connection
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
	public function retrieveById($identifier)
	{
		$result = $this->newQuery()->find($identifier);

		if ( ! is_null($result))
		{
			return new User((array) $result);
		}
	}

	/**
	 * Retrieve a user by by their unique identifier and "remember me" token.
	 *
	 * @param  mixed  $identifier
	 * @param  string  $token
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveByToken($identifier, $token)
	{
		return $this->retrieveById($identifier);
	}

	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  string  $token
	 * @return void
	 */
	public function updateRememberToken(UserInterface $user, $token)
	{
		return;
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
