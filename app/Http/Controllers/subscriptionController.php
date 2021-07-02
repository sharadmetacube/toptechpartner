<?php

namespace App\Http\Controllers;

require_once('../vendor/autoload.php');

use Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        if(!empty($plans)){
            foreach($plans as $plan){
                $fnPlan = Plan::firstOrNew(array('name' => $plan->product->name));
                $fnPlan->slug = Str::slug($plan->product->name);
                $fnPlan->stripe_plan = $plan->id;
                $fnPlan->description = $plan->product->description;
                $fnPlan->cost = ($plan->amount)/100;
                $fnPlan->created_at = date('Y-m-d H:i:s', $plan->created);
                $fnPlan->save();
            }
        }

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
        $stripe = new \Stripe\StripeClient($token);
        if($user->stripe_id!=NULL){
            $customer = $stripe->customers->update(
                $user->stripe_id,
                [
                    'address' => ['city' => $user->city , 'country' => $user->country , 'state' => $user->state , 'postal_code' => $user->postal_code , 'line1' => $user->line1 , 'line2' => $user->line2],
                    'name' => $user->name,
                    'description' => 'Customer Subscribed for plan',
                    'email' => $user->email
                ]
            );
        }else{
            $customer = $stripe->customers->create([
                'address' => ['city' => $user->city , 'country' => $user->country , 'state' => $user->state , 'postal_code' => $user->postal_code , 'line1' => $user->line1 , 'line2' => $user->line2],
                'name' => $user->name,
                'description' => 'Customer Subscribed for plan',
                'email' => $user->email
            ]);
            //update stripe customer id in user table for reference
            $updateUsers = DB::table('users')->where('id', $user->id)->update(['stripe_id' => $customer->id]);
        }

        //$user->createOrGetStripeCustomer();
        $plan = $request->input('plan'); 
        $user->addPaymentMethod($paymentMethod);      
        try {
            $user->newSubscription('default', $plan)->create($paymentMethod, [
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }
        
        return redirect()->route('user.dashboard')->with('success', 'Your plan subscribed successfully');
   }

    public function updateSubscription(Request $request){
        $user = Auth::user();
        $plan = Plan::find($request->plan);
        $getSubscription = DB::table('subscriptions')->where('user_id', auth()->user()->id)->where('stripe_status','active')->orderBy('id', 'DESC')->first();
        $update_subscription = $user->subscription($getSubscription->name)->noProrate()->swap($plan->stripe_plan);
        if($update_subscription){
            return redirect()->route('user.dashboard')->with('success', 'Your plan updated successfully and will be charged from next billing cycle !');
        }else{
            return redirect()->route('user.dashboard')->with('error', 'Error in Updating your subscription.');
        }
    }

    public function cancelSubscription(Request $request){
        $user = Auth::user();
        $getSubscription = DB::table('subscriptions')->where('user_id', auth()->user()->id)->where('stripe_status','active')->orderBy('id', 'DESC')->first();
        $cancel_subscription = $user->subscription($getSubscription->name)->cancel();
        if($cancel_subscription){
            return redirect()->route('user.dashboard')->with('success', 'Your plan cancelled successfully !');
        }else{
            return redirect()->route('user.dashboard')->with('error', 'Error in cancelling your subscription. Please contact site administrator');
        }
    }
}