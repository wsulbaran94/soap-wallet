<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('soap/client/register', [ClientController::class, 'register']);
Route::post('soap/wallet/recharge', [WalletController::class, 'rechargeWallet']);
Route::post('soap/wallet/balance', [WalletController::class, 'balance']);
Route::post('soap/payment', [PaymentController::class, 'payment']);
Route::post('soap/payment/confirm', [PaymentController::class, 'confirmPayment']);

Route::get('/wsdl', function () {
    return response()->file(public_path('wsdl/client.wsdl'));
});