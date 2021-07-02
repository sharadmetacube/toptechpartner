<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginActivity;

class LoginActivityController extends Controller
{
    public function index()
    {
        $loginActivities = LoginActivity::whereUserId(auth()->user()->id)->latest()->paginate(10);
        return view('login-activity', compact('loginActivities'));
    }
}
