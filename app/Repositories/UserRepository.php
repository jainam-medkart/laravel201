<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function getById($id)
    {
        return User::findOrFail($id);
    }

    public function getByEmail($email)
    {
        return DB::table('users')->where('email', $email)->first();
    }

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function createToken(User $user, $tokenName)
    {
        return $user->createToken($tokenName)->plainTextToken;
    }
}