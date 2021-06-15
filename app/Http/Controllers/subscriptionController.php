<?php

namespace App\Http\Controllers;

require_once('../vendor/autoload.php');

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use App\Models\Plan;
use App\Models\User;
use Laravel\Cashier\Cashier;
use \Stripe\Stripe;

class SubscriptionController extends Controller
{   
    protected $stripe;

    public function __construct() 
    {
        $this->middleware('auth');
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }

    public function create(Request $request, Plan $plan)
    {
        //dd($request->all()); 
        $plan = Plan::findOrFail($request->get('plan'));
        
        $user = $request->user();
        $paymentMethod = $request->paymentMethod;
        

        $user->createOrGetStripeCustomer();
        $user->updateDefaultPaymentMethod($paymentMethod);
        $user->newSubscription('default', $plan->stripe_plan)
            ->create($paymentMethod, [
                'email' => $user->email,
            ]);

        if(auth()->user()->role==1){
            return redirect()->route('admin.dashboard')->with('success', 'Your plan subscribed successfully');
        }elseif(auth()->user()->role==2){
            return redirect()->route('user.dashboard')->with('success', 'Your plan subscribed successfully');
        }
        
    }


    public function createPlan()
    {
        if(auth()->user()->role==1){
            return view('dashboards.admins.plans.create');
        }
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
            return redirect(route('plans.list'));
        }else{
            //create stripe product
            $stripeProduct = $this->stripe->products->create([
                'name' => $data['name'],
            ]);
            
            //Stripe Plan Creation
            $stripePlanCreation = $this->stripe->plans->create([
                'amount' => $price,
                'currency' => 'usd',
                'interval' => 'month', //  it can be day,week,month or year
                'product' => $stripeProduct->id,
            ]);

            $data['stripe_plan'] = $stripePlanCreation->id;

            Plan::create($data);
            session()->flash('success','Plan Created Successfully!');
            return redirect(route('plans.list'));
        }
        
    }

    public function retrievePlans() {
        $key = config('services.stripe.secret');
        $stripe = new \Stripe\StripeClient($key);
        $plansraw = $stripe->plans->all();
        $plans = $plansraw->data;
        
        foreach($plans as $plan) {
            $prod = $stripe->products->retrieve(
                $plan->product,[]
            );
            $plan->product = $prod;
        }
        return $plans;
    }

    public function showSubscription() { 
        $plans = $this->retrievePlans();
        $user = Auth::user();
        
        return view('dashboards.users.plans.subscribe', [
            'user'=>$user,
            'intent' => $user->createSetupIntent(),
            'plans' => $plans
        ]);
    }

    public function processSubscription(Request $request)
   {
        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');
        $token = config('services.stripe.secret');
        $options = [
            'description' => 'Customer Subscribed for plans',
            'name' => $user->name,
            'email' => $user->email,
            "address" => ["city" => $user->city, "country" => $user->country, "line1" => $user->line1, "line2" => $user->line2, "postal_code" => $user->postal_code, "state" => $user->state]
        ];
        $user->createOrGetStripeCustomer($options);
        $user->addPaymentMethod($paymentMethod);
        $plan = $request->input('plan');       
        try {
            $user->newSubscription('default', $plan)->create($paymentMethod, [
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }
        
        return redirect()->route('user.dashboard')->with('success', 'Your plan subscribed successfully');
   }
}