<?php

use App\Events\Hello;
use Illuminate\Support\Str;
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


Route::get('/broadcast', function () {
    broadcast(new Hello());

    return Str::uuid();
});


// Route::get('/test-notify', function() {
//     $sendToken = sendSMSToken('2348057481320');
//     return $sendToken;
// });