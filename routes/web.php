<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function(){
    // Page showing all plans
    Route::get('/billing',  'BillingController@index')->name('billing');
    // Page that collects billing info like card number
    Route::get('/checkout/{plan}', 'CheckoutController@checkout')->name('checkout');
    // Creates a subscription if the user submits card and other info
    Route::post('/checkout', 'CheckoutController@processCheckout')->name('checkout.process');

    //cancel subscription
    Route::get('/cancel', 'BillingController@cancel')->name('cancel');
    //resume subscription
    Route::get('/resume', 'BillingController@resume')->name('resume');
    
    Route::get('/payment-method/default/{id}', 'PaymentMethodController@markDefault')->name('payment-method.markDefault');
    Route::resource('/payment-method', 'PaymentMethodController')->only(['create', 'update', 'store', 'destroy']);    
    
    Route::post('stripe/webhook/charge-succeeded', 'WebhookController@handleWebhook');
    
});

Route::stripeWebhooks('stripeWebhook');
