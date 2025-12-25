<?php

namespace App\Interfaces;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

interface IAuthRepository
{
    public function login(LoginRequest $request): array;
    public function logout(Request $request): void;
}

