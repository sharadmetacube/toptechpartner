<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\plan;

class PlanController extends Controller
{   
    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function index()
    {
        $user = Auth::user();
        $plans = Plan::all();
        $getSubscription = DB::table('subscriptions')->where('user_id', $user->id)->where('stripe_status','active')->orderBy('id', 'DESC')->first();
        if($getSubscription){
            $planObj = Plan::where('stripe_plan',$getSubscription->stripe_plan)->first();
            $planObj->cancels_at = $getSubscription->ends_at;
        }else{
            $planObj = '';
        }
        $planSubscribed = empty($planObj) ? '' : $planObj;
        if(auth()->user()->role==1){
            return view('dashboards.admins.plans.index', compact('plans'));
        }elseif(auth()->user()->role==2){
            return view('dashboards.users.plans.index')->with(['user'=>$user,'plans'=>$plans,'planSubscribed'=>$planSubscribed]);
        }
    }

    /**
     * Show the Plan.
     *
     * @return mixed
     */
    public function show(Plan $plan, Request $request)
    {   
        $paymentMethods = $request->user()->paymentMethods();
        //Check for existing subscriptions
        $getSubscription = DB::table('subscriptions')->where('user_id', auth()->user()->id)->where('stripe_status','active')->orderBy('id', 'DESC')->first();
        if($getSubscription){
            $subscription = $getSubscription;
        }else{
            $subscription = '';
        }

        $intent = $request->user()->createSetupIntent();
        
        if(auth()->user()->role==1){
            return view('dashboards.admins.plans.show', compact('plan', 'intent'));
        }elseif(auth()->user()->role==2){
            return view('dashboards.users.plans.show', compact('plan', 'intent', 'subscription'));
        }
    }
}