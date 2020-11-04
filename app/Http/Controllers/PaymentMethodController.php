<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function create()
    {             
        $intent = auth()->user()->createSetupIntent();                                
        return view('payment-method.create')->with('intent', $intent);
    }

    public function store(Request $request)
    {
        try{                
            auth()->user()->addPaymentMethod($request->input('payment-method'));
            if($request->input('default-payment-method') == 1){
                auth()->user()->updateDefaultPaymentMethod($request->input('payment-method'));
            }            
        }
        catch(\Exception $e){
            return redirect()->back()->withError($e->getMessage());
        } 
        
        return redirect()->route('billing')->withMessage('Payment Method Added Successfully !!');
    }

    public function markDefault(Request $request, $PaymentMethodId)
    {
        try{                                        
            auth()->user()->updateDefaultPaymentMethod($PaymentMethodId);
        }
        catch(\Exception $e){
            return redirect()->back()->withError($e->getMessage());
        } 
        
        return redirect()->route('billing')->withMessage('Payment Method Added Successfully !!');
    }

    public function destroy($id)
    {
        //
    }
}
