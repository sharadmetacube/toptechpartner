<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facacdes\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
        'name' => 'required|string',    
        'email' => 'required|string|unique:users,email|max:255',
        'password' => 'required|string|confirmed|min:8'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        $token = $user->createToken('AuthToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response , 201);
    }
}
