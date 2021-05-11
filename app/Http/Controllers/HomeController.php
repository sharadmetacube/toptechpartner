<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PersonalAccessTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        $token = PersonalAccessTokens::select('plainText')->where('tokenable_id',$user->id)->first();
        if($token==null){
            $plainText = '';
        }else{
            $plainText = $token->plainText;
        }
        return view('home')->with(['user'=>$user,'token'=>$plainText]);
    }
}
