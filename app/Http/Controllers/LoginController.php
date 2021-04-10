<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required',
            'is_admin' => 'required'
        ],);

        $credentials = $request->only('email', 'password', 'is_admin');

        if (Auth::attempt($credentials)) {
            return  response()->json(Auth::user(), 200);
        }
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.']
        ]);
    }
    public function logout()
    {
        Auth::logout();
    }
}