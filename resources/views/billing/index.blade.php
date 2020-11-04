@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"> My Plan </div>                
                
                <div class="card-body">
                    @if(session('message'))
                        <div class="alert alert-info"> {!! session('message') !!} </div>                    
                    @endif

                    @if (is_null($currentPlan))
                       <div class="alert alert-info text-center"> You are currently using a <strong> Free Trial </strong>. Please subscribe upgrade to one of our paid plans. </div>                                      
                    @endif
                                        
                    <div class="row justify-content-center">                        
                        @foreach ($plans as $plan)
                            <div class="card text-center col-md-3 mx-3">
                                <div class="card-header">
                                    Subscription Plan
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"> {{ $plan->name }} </h5>
                                    <p class="card-text"> ${{ number_format($plan->price / 100, 2) }} / month </p>
                                    <hr />

                                    {{-- if a plan in the loop matches the current plan of the user then show this section. Else... --}}                                    
                                    @if(!is_null($currentPlan) && $plan->stripe_plan_id == $currentPlan->stripe_plan)
                                        <p> Your Current Plan </p>                                        
                                        {{-- if a user's current plan is not on grace period then show this section. Else... --}}
                                        @if(!$currentPlan->onGracePeriod())
                                            {{-- Cancel Subscription --}}
                                            <a href="{{ route('cancel') }}" class="btn btn-danger" onclick="return confirm('Are You Sure ?')"> Cancel Subscription </a>
                                        @else
                                            {{-- If the subscription is already cancelled, then the current user plan's
                                            ends_at(from database) is converted to a date string(using carbon) and shown --}}                                            
                                            <div class="alert alert-info"> Your Subscription Will End At {{ $currentPlan->ends_at->toDateString() }} </div>                                                                                        
                                            <a href="{{ route('resume') }}" class="btn btn-primary" onclick="return confirm('Are You Sure ?')"> Resume Subscription </a>
                                        @endif
                                    @else                                    
                                        <a href="{{ route('checkout', $plan->id) }}" class="btn btn-primary"> Subscribe </a>
                                    @endif                                    
                                </div>
                                <div class="card-footer text-muted">
                                    1 month
                                </div>
                            </div>                            
                        @endforeach                                                
                    </div>
                </div>
            </div>

            @if(!is_null($currentPlan))
                <div class="card">
                    <div class="card-header"> Your Payment Methods </div>                    
                    <div class="card-body">                                                
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th> Brand </th>
                                    <th> Expires at </th>
                                    <th> Default </th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($paymentMethods as $index => $paymentMethod)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td>{{ $paymentMethod->card->brand }}</td>
                                    <td>{{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }}</td>
                                    <td>
                                        @if($paymentMethod->id == $defaultPaymentMethod->id)
                                            Active
                                        @else
                                            <a href="{{ route('payment-method.markDefault', $paymentMethod->id) }}" class="btn btn-primary"> Set As Default </a>
                                        @endif                                        
                                    </td>
                                </tr>                                
                            @endforeach
                            </tbody>                                
                        </table>    
                        <br>
                        <a href="{{ route('payment-method.create') }}" class="btn btn-primary"> Add Payment method </a>                    
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
