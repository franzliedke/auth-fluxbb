<?php

use FranzLiedke\AuthFluxBB\User;
use Mockery as m;
class LoginTest extends PHPUnit_Framework_TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testLoginSuccess()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('FranzLiedke\AuthFluxBB\CookieStorage');
		$event = m::mock('Illuminate\Events\Dispatcher');
		$guard = new FranzLiedke\AuthFluxBB\Guard($provider, $cookieStorage);
		$guard->setDispatcher($event);

		$credentials = ['username' => 'user', 'password' => 'pass'];
		$user = new User($credentials);
		$user->id = 1234;
		$provider->shouldReceive('retrieveByCredentials')->once()->with($credentials)->andReturn($user);
		$provider->shouldReceive('validateCredentials')->once()->with($user, $credentials)->andReturn(true);
		$cookieStorage->shouldReceive('login')->once()->with(1234, 'pass', false);

		$event->shouldReceive('fire')->once()->with('auth.attempt',[$credentials, false, true]);
		$event->shouldReceive('fire')->once()->with('auth.login', [$user, false]);

		$guard->attempt($credentials);

		$this->assertTrue($guard->check());
		$this->assertEquals($guard->user(), $user);
	}

	public function testLoginFailure()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('FranzLiedke\AuthFluxBB\CookieStorage');
		$guard = new FranzLiedke\AuthFluxBB\Guard($provider, $cookieStorage);

		$credentials = ['username' => 'user', 'password' => 'pass'];
		$user = new User($credentials);
		$user->id = 1234;

		$provider->shouldReceive('retrieveByCredentials')->once()->with($credentials)->andReturn($user);
		$provider->shouldReceive('validateCredentials')->once()->with($user, $credentials)->andReturn(false);
		$cookieStorage->shouldReceive('login')->never();
		$cookieStorage->shouldReceive('getId')->times(3)->andReturn(null);

		$guard->attempt($credentials);

		$this->assertFalse($guard->check());
		$this->assertNull($guard->user());
		$this->assertTrue($guard->guest());
	}

	public function testCookieLogin()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('FranzLiedke\AuthFluxBB\CookieStorage');
		$guard = new FranzLiedke\AuthFluxBB\Guard($provider, $cookieStorage);

		$user = new User(array('password' => 'pass'));
		$user->id = 1234;
		$cookieStorage->shouldReceive('getId')->once()->andReturn([1234, false]);
		$provider->shouldReceive('retrieveById')->once()->with(1234)->andReturn($user);
		$cookieStorage->shouldReceive('login')->once()->with(1234, 'pass', false);

		$this->assertEquals($guard->user(), $user);
		$this->assertTrue($guard->check());
	}

	public function testIdLogin()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('FranzLiedke\AuthFluxBB\CookieStorage');
		$guard = new FranzLiedke\AuthFluxBB\Guard($provider, $cookieStorage);

		$credentials = ['username' => 'user', 'password' => 'pass'];
		$user = new User($credentials);
		$user->id = 1234;
		$provider->shouldReceive('retrieveById')->once()->with(1234)->andReturn($user);
		$cookieStorage->shouldReceive('login')->once()->with(1234, 'pass', false);

		$guard->loginUsingId(1234);

		$this->assertEquals($guard->user(), $user);
		$this->assertTrue($guard->check());
	}

	public function testLoggedOutByDefault()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('FranzLiedke\AuthFluxBB\CookieStorage');
		$guard = new FranzLiedke\AuthFluxBB\Guard($provider, $cookieStorage);

		$cookieStorage->shouldReceive('getId')->once()->andReturn(null);

		$this->assertTrue($guard->guest());
	}

	public function testLogout()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('FranzLiedke\AuthFluxBB\CookieStorage');
		$guard = new FranzLiedke\AuthFluxBB\Guard($provider, $cookieStorage);

		$credentials = ['username' => 'user', 'password' => 'pass'];
		$user = new User($credentials);
		$user->id = 1234;
		$provider->shouldReceive('retrieveById')->once()->with(1234)->andReturn($user);
		$cookieStorage->shouldReceive('login')->once()->with(1234, 'pass', false);

		$guard->loginUsingId(1234);

		$this->assertEquals($guard->user(), $user);

		$cookieStorage->shouldReceive('logout')->once();
		$provider->shouldReceive('updateRememberToken')->once();

		$guard->logout();

		$this->assertTrue($guard->guest());
		$this->assertNull($guard->user());
	}

	public function tearDown()
	{
		m::close();
	}
}