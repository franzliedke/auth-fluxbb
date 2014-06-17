<?php namespace FranzLiedke\AuthFluxBB;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class CookieStorage {

	/**
	 * The current request instance.
	 *
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	protected $request;

	/**
	 * The config parser instance.
	 *
	 * @var \FranzLiedke\AuthFluxBB\ConfigParser
	 */
	protected $parser;

	/**
	 * Create a new cookie storage.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @param  \FranzLiedke\AuthFluxBB\ConfigParser  $parser
	 * @return void
	 */
	public function __construct(Request $request, ConfigParser $parser)
	{
		$this->request = $request;
		$this->parser = $parser;
	}

	/**
	 * Write a cookie for the given user with the given password hash.
	 *
	 * @param  int  $id
	 * @param  string  $password
	 * @param  bool  $remember
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function login($id, $password, $remember)
	{
		if ($remember)
		{
			$expire = time() + 1209600;
		}
		else
		{
			$expire = time() + 1800;
		}

		return $this->setcookie($id, $password, $expire);
	}

	/**
	 * Destroy the cookie for the current user.
	 *
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function logout()
	{
		$id = 1;
		$hash = sha1(uniqid(rand(), true));

		// The cookie expires after a year
		$expire = time() + 31536000;

		// Overwrite our cookie with one for a guest
		return $this->setcookie($id, $hash, $expire);
	}

	/**
	 * Set a cookie, FluxBB style!
	 *
	 * @param  int  $id
	 * @param  string  $hash
	 * @param  int  $expire
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	protected function setcookie($id, $hash, $expire)
	{
		if ($expire - time() < 1)
			$expire = 0;

		$seed = $this->parser->get('cookie_seed');
		$name = $this->parser->get('cookie_name');
		$path = $this->parser->get('cookie_path');
		$domain = $this->parser->get('cookie_domain');
		$secure = $this->parser->get('cookie_secure');
		$httpOnly = true;

		$hmacPassword = $this->hmac($hash, $seed.'_password_hash');
		$hmacExpire = $this->hmac($id.'|'.$expire, $seed.'_cookie_hash');
		$content = $id.'|'.$hmacPassword.'|'.$expire.'|'.$hmacExpire;

		setcookie($name, $content, $expire, $path, $domain, $secure, $httpOnly);

		return null;
	}

	/**
	 * Calculate a HMAC digest as FluxBB understands it.
	 *
	 * @param  string  $data
	 * @param  string  $key
	 * @return string
	 */
	protected function hmac($data, $key)
	{
		return hash_hmac('sha1', $data, $key, false);
	}

	/**
	 * Read the current user's identifier from the cookie.
	 *
	 * @return id|null
	 */
	public function getId()
	{
		$cookieName = $this->parser->get('cookie_name');

		$content = array_get($_COOKIE, $cookieName);

		if ( ! is_null($content) && preg_match('%^(\d+)\|([0-9a-fA-F]+)\|(\d+)\|([0-9a-fA-F]+)$%', $content, $matches))
		{
			return intval($matches[1]);
		}

		return null;
	}

}
