<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;

class UserService
{
    /**
     * @var Authenticatable
     */
    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('sanctum')->user();
    }

    /**
     * Get login user details
     * @return Authenticatable
     */
    public function getLoggedUser()
    {
        return $this->user;
    }

    /**
     * Update user
     * 
     * @param array $data
     */
    public function update($data)
    {
        $this->user->update($data);
    }

    /**
     * Delete user
     */
    public function delete()
    {
        $this->user->delete();
    }
}
