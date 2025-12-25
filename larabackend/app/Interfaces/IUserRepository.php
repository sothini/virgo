<?php

namespace App\Interfaces;
use App\Models\User;

interface IUserRepository {
    public function getProfile(): User;
}
