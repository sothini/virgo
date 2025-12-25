<?php

namespace App\Repositories;

use App\Interfaces\IUserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository implements IUserRepository
{
    public function getProfile(): User
    {
        return User::with('assets')->find(Auth::id());
    }
}
