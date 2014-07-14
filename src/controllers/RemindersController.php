<?php
class RemindersController extends Controller {

	/**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	public function getRemind()
	{
		return View::make('password.remind');
	}

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return Response
	 */
	public function postRemind()
	{
		switch ($response = Password::remind(Input::only('email')))
		{
			case Password::INVALID_USER:
				return Redirect::back()->with('error', Lang::get($response));

			case Password::REMINDER_SENT:
				return Redirect::back()->with('status', Lang::get($response));
		}
	}


	public function getSet($userId = null, $token = null)
	{
		if (is_null($token) || is_null($userId)) App::abort(404);

		$credentials = array('id' => $userId, 'token' => $token, 'password' => 'fakenewpassword', 'password_confirmation' => 'fakenewpassword');

		$response = Password::reset($credentials, function($user, $password)
		{
			DB::table('users')->where('id', $user->id)->update(['password' => $user->activate_string]);
		});

		switch ($response)
		{
			case Password::INVALID_PASSWORD:
			case Password::INVALID_TOKEN:
			case Password::INVALID_USER:
				return Redirect::action('RemindersController@getRemind')->with('error', Lang::get($response));

			case Password::PASSWORD_RESET:
				return Redirect::to('/');
		}
	}
}
