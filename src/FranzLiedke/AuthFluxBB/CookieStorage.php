<?php namespace FranzLiedke\AuthFluxBB;

class CookieStorage {

	/**
	 * The config parser instance.
	 * 
	 * @var \Franzliedke\AuthFluxBB\ConfigParser
	 */
	protected $parser;

	/**
	 * Create a new cookie storage.
	 *
	 * @param  \FranzLiedke\AuthFluxBB\ConfigParser  $parser
	 * @return void
	 */
	public function __construct(ConfigParser $parser)
	{
		$this->parser = $parser;
	}

	/**
	 * Write a cookie for the given user with the given password hash.
	 *
	 * @param  int  $id
	 * @param  string  $password
	 * @return void
	 */
	public function login($id, $password)
	{
		$this->setGlobals();

		// Set the cookie with a sensible visit timeout
		$expire = time() + 1800;
		pun_setcookie($id, $password, $expire);

		// Reset tracked topics
		set_tracked_topics(null);
	}

	/**
	 * Destroy the cookie for the current user.
	 * 
	 * @return void
	 */
	public function logout()
	{
		$this->setGlobals();

		// Overwrite our cookie with one for a guest
		pun_setcookie(1, pun_hash(uniqid(rand(), true)), time() + 31536000);
	}

	/**
	 * Read the current user's identifier from the cookie.
	 * 
	 * @return id|null
	 */
	public function getId()
	{
		// TODO
	}

	/**
	 * Set the global variables needed by FluxBB's authentication methods.
	 *
	 * @return void
	 */
	protected function setGlobals()
	{
		global $cookie_name,
		       $cookie_seed,
		       $cookie_path,
		       $cookie_domain,
		       $cookie_secure,
		       $pun_config;

		$cookie_name   = $this->parser->get('cookie_name');
		$cookie_seed   = $this->parser->get('cookie_seed');
		$cookie_path   = $this->parser->get('cookie_path');
		$cookie_domain = $this->parser->get('cookie_domain');
		$cookie_secure = $this->parser->get('cookie_secure');
		$pun_config    = array('o_timeout_visit' => 1800);
	}

}
