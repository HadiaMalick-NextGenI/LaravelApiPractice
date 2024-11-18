<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showSignupForm()
    {
        return view('v1.auth.signup');
    }

    public function showLoginForm()
    {
        return view('v1.auth.login');
    }
}
