<?php namespace FranzLiedke\AuthFluxBB;

use Illuminate\Auth\Reminders\DatabaseReminderRepository as DbRepository;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Connection;

class FluxReminderRepository extends DbRepository {

	function __construct(Connection $connection) {
		$this->connection = $connection;
	}

	/**
	 * Create a new reminder record and token.
	 *
	 * @param  \Illuminate\Auth\Reminders\RemindableInterface  $user
	 * @return string
	 */
	public function create(RemindableInterface $user)
	{
		$email = $user->getReminderEmail();

		$new_pass = $this->randomPass();
		$new_pass_key = $this->randomPass();
		$current_time = time();

		$this->getTable()->where('email', $email)->update(['activate_string' => sha1($new_pass), 'activate_key' => $new_pass_key, 'last_email_sent' => $current_time]);

		return ['activate_key' => $new_pass_key, 'password' => $new_pass];
	}

	/**
	 * Determine if a reminder record exists and is valid.
	 *
	 * @param  \Illuminate\Auth\Reminders\RemindableInterface  $user
	 * @param  string  $token
	 * @return bool
	 */
	public function exists(RemindableInterface $user, $token)
	{
		$email = $user->getReminderEmail();

		$reminder = $this->getTable()->where('email', $email)->where('activate_key', $token)->count();

		return $reminder === 1;
	}

	/**
	 * Determine if the reminder has expired.
	 *
	 * @param  array  $reminder
	 * @return bool
	 */
	protected function reminderExpired($reminder)
	{
		return false;
	}

	/**
	 * Delete a reminder record by token.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function delete($token)
	{
		$this->getTable()->where('activate_key', $token)->update(['activate_string' => null, 'activate_key' => null]);
	}

	/**
	 * Delete expired reminders.
	 *
	 * @return void
	 */
	public function deleteExpired()
	{
		return;
	}

	protected function getTable()
	{
		return $this->connection->table('users');
	}

	protected function randomPass($length = 8)
	{
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = '';
		$alphaLength = strlen($alphabet) - 1;
		for ($i = 0; $i < $length; $i++) {
			$n = rand(0, $alphaLength);
			$pass .= $alphabet[$n];
		}
		return $pass;
	}

}