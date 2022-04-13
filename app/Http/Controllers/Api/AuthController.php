<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * Authentication service
     * @var App\Services\AuthService
     */
    private $authService;

    /**
     * Register service
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function isAuthenticated()
    {
        return $this->authService->isAuthenticated();
    }



    /**
     * Register user
     * 
     * @param  App\Http\Requests\RegisterRequest $request
     * @return Response
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->createUser($request->all());

        return response()->json([
            'type' => 'UserRegistered',
            'user' => $user,
        ]);
    }

    /**
     * Login user
     * 
     * @param  App\Http\Requests\LoginRequest $request
     * @return Response
     */
    public function login(LoginRequest $request)
    {
        $this->authService->attemptLogin($request->all())->generateToken($request->token_name, true);

        return response()->json([
            'type' => 'UserLoggedIn',
            'data' => [
                'token' => $this->authService->token->plainTextToken,
                'user' => $this->authService->loggedUser
            ]
        ]);
    }

    /**
     * Logout from current device
     */
    public function logout()
    {
        if ($this->authService->isAttempted) {
            $this->authService->logout();
            
            return response()->json([
                'type' => 'UserLoggedOut',
                'message' => 'User logged out successfully'
            ]);
        }

        throw new AuthenticationException();
    }

    /**
     * Logout from all device
     */
    public function logoutAll()
    {
        if ($this->authService->isAttempted) {
            $this->authService->logoutAll();

            return response()->json([
                'type' => 'UserLoggedOut',
                'message' => 'User logged out from all devices successfully'
            ]);
        }

        throw new AuthenticationException();
    }
}
