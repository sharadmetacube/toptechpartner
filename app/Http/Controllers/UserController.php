<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plan;
use App\Models\PersonalAccessTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $user = Auth::user();
        $token = PersonalAccessTokens::select('plainText','token_count')->where('tokenable_id',$user->id)->first();
        $getSubscription = DB::table('subscriptions')->where('user_id', $user->id)->where('stripe_status','active')->orderBy('id', 'DESC')->first();
        if($getSubscription){
            $planObj = Plan::where('stripe_plan',$getSubscription->stripe_plan)->first();
            $planObj->cancels_at = $getSubscription->ends_at;
        }else{
            $planObj = '';
        }
        $plan = empty($planObj) ? '' : $planObj;
        $plainText = ($token==null) ? '' : $token;
        return view('dashboards.users.index')->with(['user'=>$user,'token'=>$plainText,'plan'=>$plan]);
    }

    public function profile(){
        $user = Auth::user();
        return view('dashboards.users.profile')->with(['user'=>$user]);
    }

    public function profileUpdate(User $user){
        if(Auth::user()->email == request('email')) {
            $this->validate(request(), [
                'name' => ['required'],
                'city' => ['required', 'string', 'max:30'],
                'state' => ['required', 'string', 'max:60'],
                'country' => ['required', 'string', 'max:60'],
                'postal_code' => ['required'],
                'line1' => ['required', 'string', 'max:255'],
            ]);

            $user->name = request('name');
            $user->city = request('city');
            $user->state = request('state');
            $user->country = request('country');
            $user->postal_code = request('postal_code');
            $user->line1 = request('line1');
            $user->line2 = request('line2');
            $user->save();

            return redirect()->back()->with('success', 'Your Profile Updated successfully');

        }else{
            return redirect()->back()->with('error', 'Cannot Change your EMAIL ID.');
        }
    }

}
