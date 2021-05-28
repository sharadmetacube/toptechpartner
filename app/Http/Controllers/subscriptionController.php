<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\User;
//use Laravel\Cashier\StripeGateway;

class SubscriptionController extends Controller
{   
    protected $stripe;

    public function __construct() 
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }

    public function create(Request $request, Plan $plan)
    {
        $plan = Plan::findOrFail($request->get('plan'));
        
        $user = $request->user();
        //$stripe = new StripeGateway($user);
        $paymentMethod = $request->paymentMethod;
        

        $user->createOrGetStripeCustomer();
        $user->updateDefaultPaymentMethod($paymentMethod);
        $user->newSubscription('default', $plan->stripe_plan)
            ->create($paymentMethod, [
                'email' => $user->email,
            ]);
        
        return redirect()->route('home')->with('success', 'Your plan subscribed successfully');
    }


    public function createPlan()
    {
        return view('plans.create');
    }

    public function storePlan(Request $request)
    {   
        $data = $request->except('_token');

        $data['slug'] = strtolower($data['name']);
        $price = $data['cost'] *100; 

        //Check if plan already exists
        $checkPlan = Plan::where('slug',$data['slug'])->first();
        if($checkPlan){
            session()->flash('warning','Plan Already Exists!');
            return redirect(route('plans.index'));
        }else{
            //create stripe product
            $stripeProduct = $this->stripe->products->create([
                'name' => $data['name'],
            ]);
            
            //Stripe Plan Creation
            $stripePlanCreation = $this->stripe->plans->create([
                'amount' => $price,
                'currency' => 'inr',
                'interval' => 'month', //  it can be day,week,month or year
                'product' => $stripeProduct->id,
            ]);

            $data['stripe_plan'] = $stripePlanCreation->id;

            Plan::create($data);
            session()->flash('success','Plan Created Successfully!');
            return redirect(route('plans.index'));
        }
        
    }
}