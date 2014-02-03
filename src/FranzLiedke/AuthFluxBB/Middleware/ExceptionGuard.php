<?php namespace FranzLiedke\AuthFluxBB\Middleware;

use Illuminate\Cookie\Guard;
use Illuminate\Encryption\Encrypter;
use Illuminate\Encryption\DecryptException; 
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExceptionGuard extends Guard {

	/**
	 * The name of the cookie that should not be encrypted.
	 *
	 * @var string
	 */
	protected $cookieName;

	/**
	 * Create a new ExceptionGuard instance.
	 *
	 * @param  \Symfony\Component\HttpKernel\HttpKernelInterface  $app
	 * @param  \Illuminate\Encryption\Encrypter  $encrypter
	 * @param  string
	 * @return void
	 */
	public function __construct(HttpKernelInterface $app, Encrypter $encrypter, $cookieName)
	{
		$this->app = $app;
		$this->encrypter = $encrypter;
		$this->cookieName = $cookieName;
	}

	/**
	 * Decrypt the cookies on the request.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @return \Symfony\Component\HttpFoundation\Request
	 */
	protected function decrypt(Request $request)
	{
		foreach ($request->cookies as $key => $c)
		{
			try
			{
				// Do not encrypt the given cookie
				if ($key == $this->cookieName) continue;

				$request->cookies->set($key, $this->decryptCookie($c));
			}
			catch (DecryptException $e)
			{
				$request->cookies->set($key, null);
			}
		}

		return $request;
	}

	/**
	 * Encrypt the cookies on an outgoing response.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Response  $response
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function encrypt(Response $response)
	{
		foreach ($response->headers->getCookies() as $key => $c)
		{
			// Do not encrypt the given cookie
			if ($c->getName() == $this->cookieName) continue;

			$encrypted = $this->encrypter->encrypt($c->getValue());

			$response->headers->setCookie($this->duplicate($c, $encrypted));
		}

		return $response;
	}

}