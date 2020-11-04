<?php

namespace App\Http\Controllers;

use App\Plan;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(){
                
        $plans = Plan::all();  
        // Gets the stripe_plan if a user is already subscribed(which is a subscription ID).
        // If user is not subscribed to a plan, stores null.
        $currentPlan = auth()->user()->subscription('default') ?? NULL;        
        // get payment methods for current user                
        $paymentMethods = auth()->user()->paymentMethods(); 
        
        $defaultPaymentMethod = auth()->user()->defaultPaymentMethod();        

        return view('billing.index')
        ->with('plans', $plans)
        ->with('currentPlan', $currentPlan)
        ->with('paymentMethods', $paymentMethods)
        ->with('defaultPaymentMethod', $defaultPaymentMethod);
        
    }

    public function cancel(){
        auth()->user()->subscription('default')->cancel();
        return redirect()->route('billing');    
    }

    public function resume(){
        auth()->user()->subscription('default')->resume();        
        return redirect()->route('billing');    
    }
}
