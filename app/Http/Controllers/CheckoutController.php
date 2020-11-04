<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;
use App\User;

class CheckoutController extends Controller
{
    public function checkout(Plan $plan){                  
                
        //get current plan of the logged in user
        $currentPlan = auth()->user()
        ->subscription('default')->stripe_plan ?? NULL;       

        //if current plan exists and the current plan is not equal to the route plan id(say if the user is already a bronze
        //but somehow accesses this route for buying a bronze, then it goes to else)
        if(!is_null($currentPlan) && $currentPlan != $plan->stripe_plan_id){
            auth()->user()->subscription('default')->swap($plan->stripe_plan_id);
            return redirect()->route('billing');
        }
        elseif($currentPlan == $plan->stripe_plan_id){            
            
            return redirect()->route('billing')
            ->withMessage('You are already subscribed to our <strong>' . $plan->name . '</strong>');            
        }     

        $intent = auth()->user()->createSetupIntent();
        
        return view('billing.checkout')->with('plan', $plan)->with('intent', $intent);
    }

    public function processCheckout(Request $request)
    {                
        $plan = Plan::find($request->input('billing_plan_id'));        
        try{
            auth()->user()->newSubscription('default', $plan->stripe_plan_id)
            ->create($request->input('payment-method'));
            // after a user pays for a plan, set his trial_ends_at to null
            auth()->user()->update(['trial_ends_at' => NULL]);
            return redirect()->route('billing')->withMessage('Subscribed Successfully !!');
        }
        catch(\Exception $e){
            return redirect()->back()->withError($e->getMessage());
        }       
    }
}