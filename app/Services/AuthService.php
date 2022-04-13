<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
	/**
	 * is user attempt Login
	 * @var boolean
	 */
	public $isAttempted = false;

	/**
	 * logged user collection
	 * @var \Collection
	 */
	public $loggedUser = null;

	/**
	 * User token instance
	 * @var Laravel\Sanctum\NewAccessToken
	 */
	public $token = null;

	/**
	 * Check is user already logged in or not
	 * @return boolean
	 */
	public function __construct()
	{
		if (Auth::guard('sanctum')->check()) {
			$this->setLoggedUser(Auth::guard('sanctum')->user());
		}
	}

	/**
	 * Check is request authenticated or not
	 */
	public function isAuthenticated()
	{
		if($this->isAttempted) {
			return response()->json([
				'type' => 'UserAuthenticated',
				'code' => 200
			]);
		}

		throw new AuthenticationException();
	}

	/**
	 * Register user
	 * 
	 * @param  array $data
	 * @return App\Models\User
	 */
	public function createUser($data)
	{
		return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => Hash::make($data['password'])
		]);
	}

	/**
	 * Attempt for login
	 * 
	 * @param  array $credentials
	 * @return $this
	 * @throws Illuminate\Validation\ValidationException
	 */
	public function attemptLogin($credentials)
	{
		$this->isAttempted = Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $credentials['remember_me'] ?? false);
		if ($this->isAttempted) {
			$this->loggedUser = Auth::user();
			return $this;
		}

		throw ValidationException::withMessages([
			'email' => 'The provided credentials do not match our records.',
		]);
	}

	/**
	 * Generate user token
	 * 
	 * @param  string $token_name
	 * @param  boolean $revokeAll - Whether revoke all tokens while create new one
	 * @return $this
	 */
	public function generateToken($token_name, $revokeAll = false)
	{
		if ($this->isAttempted) {
			if ($revokeAll) {
				$this->revokeAllToken();
			}
			$this->token = $this->loggedUser->createToken($token_name);
			return $this;
		}

		throw new AuthenticationException();
	}

	/**
	 * Logut from current device
	 */
	public function logout()
	{
		if ($this->isAttempted) {
			$this->loggedUser->currentAccessToken()->delete();
		}
	}

	/**
	 * Logout from all device
	 */
	public function logoutAll()
	{
		if ($this->isAttempted) {
			$this->revokeAllToken();
		}
	}

	/**
	 * Revoke all tokens
	 */
	public function revokeAllToken()
	{
		$this->loggedUser->tokens()->delete();
	}

	/**
	 * Set up attempt
	 * 
	 * @param Authenticatable $loggedUser 
	 */
	public function setLoggedUser(Authenticatable $loggedUser)
	{
		$this->loggedUser = $loggedUser;
		$this->setIsAttempted(true);
	}

	/**
	 * Set up attempt
	 * 
	 * @param boolean $isAttempted 
	 */
	public function setIsAttempted(bool $isAttempted)
	{
		$this->isAttempted = $isAttempted;
	}
}
