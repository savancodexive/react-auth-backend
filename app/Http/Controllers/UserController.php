<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * User model service
     * @var UserService
     */
    private $userService;

    /**
     * Register UserService
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get logged in user collection
     * @return Response
     */
    public function getLoggedUser()
    {
        return response()->json([
            'user' => $this->userService->getLoggedUser()
        ]);
    }

    /**
     * Update user account
     * @param  UserRequest $request
     * @return Response
     */
    public function update(UserRequest $request)
    {
        $this->userService->update($request->only('name', 'email'));

        return response()->json([
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Deactivate user account
     * @return Response
     */
    public function delete()
    {
        $this->userService->delete();

        return response()->json([
            'message' => 'Accout deactivate successfully'
        ]);
    }
}
