<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $plans = Plan::all();
        if(auth()->user()->role==1){
            return view('dashboards.admins.plans.index', compact('plans'));
        }elseif(auth()->user()->role==2){
            return view('dashboards.users.plans.index', compact('plans'));
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

        $intent = $request->user()->createSetupIntent();
        
        if(auth()->user()->role==1){
            return view('dashboards.admins.plans.show', compact('plan', 'intent'));
        }elseif(auth()->user()->role==2){
            return view('dashboards.users.plans.show', compact('plan', 'intent'));
        }
    }
}