<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PersonalAccessTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function index(){
        $user_id = Auth::id();
        $user = User::find($user_id);
        $token = PersonalAccessTokens::select('plainText','token_count')->where('tokenable_id',$user->id)->first();
        if($token==null){
            $plainText = '';
        }else{
            $plainText = $token;
        }
        return view('dashboards.users.index')->with(['user'=>$user,'token'=>$plainText]);
    }

    function profile(){
        return view('dashboards.users.profile');
    }
}
