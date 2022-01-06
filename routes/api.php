<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Authentication;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group( function ()
{ 
    Route::get('health', function() {
        return [
            'status' => 200,
            'message' => 'Api version 1 is in good health'
        ];
    });

    // Route::group(['prefix' => 'desk'], function () {
    //     Route::post('contact', [Desk::class, 'contact']);
    //     Route::get('faq', [Desk::class, 'faq']);
    //     Route::get('promo/banner', [Desk::class, 'getBanner']);

    //     Route::group(['prefix' => 'blog'], function () {
    //         Route::get('/posts/{category?}', [Blog::class, 'index']);
    //         Route::get('/post/{slug}', [Blog::class, 'get']);
    //         Route::post('/post/create', [Blog::class, 'create']);
    //         Route::post('/post/update', [Blog::class, 'update']);
    //         Route::post('/post/delete', [Blog::class, 'delete']);
    //         Route::post('/post/category/create', [Blog::class, 'categoryCreate']);
    //         Route::get('/categories', [Blog::class, 'getCategories']);
    //     });
    // });

    /**
     * Authentication Routes
     */
    Route::prefix('auth')->group(function () {

        Route::post('sign-up', [Authentication::class, 'register']);
        Route::post('sign-in', [Authentication::class, 'login']);


        //Password Routes
        Route::prefix('password')->group(function () {
            Route::post('forget', [Authentication::class, 'forgotPassword']);
            Route::post('reset', [Authentication::class, 'resetPassword']);
        });

        Route::middleware(['auth:api'])->group(function () {
            
            Route::post('refresh', [Authentication::class, 'token_refresh']);
            Route::post('sign-out', [Authentication::class, 'logout']);

            //Verification Routes
            Route::post('email/verify', [Authentication::class, 'verifyEmail']);
            Route::post('email/resend-code', [Authentication::class, 'resendEmailVerification']);
            Route::post('phone/verify', [Authentication::class, 'verifyPhone']);
            Route::post('phone/resend-code', [Authentication::class, 'resendPhoneVerification']);
            Route::post('otp/challenge', [Authentication::class, 'sendOtp']);
            Route::post('otp/verify', [Authentication::class, 'verifyOtp']);
        });
    });

    
});