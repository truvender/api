<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\{
    Bills,
    Wallet,
    Profiles,
    Dashboards,
    Authentication,
    Posts,
};

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

    Route::group(['prefix' => 'desk'], function () {
    //     Route::post('contact', [Desk::class, 'contact']);
    //     Route::get('faq', [Desk::class, 'faq']);
    //     Route::get('promo/banner', [Desk::class, 'getBanner']);

        Route::group(['prefix' => 'blog'], function () {
            Route::get('/post/{slug}', [Posts::class, 'get']);
            Route::post('/post/create', [Posts::class, 'create']);
            Route::post('/post/update', [Posts::class, 'update']);
            Route::post('/post/delete', [Posts::class, 'delete']);
        });
    });

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


    Route::middleware(['auth:api'])->group(function () {
        /**
         * User Routes
         */
        Route::get('/session/user', [Dashboards::class, 'userSessionData']);

        /**
         * User wallet routes
         */
        Route::group(['prefix' => 'wallet'], function () {

            Route::post('wallets', [Wallet::class, 'userWallet']);
            Route::group(['prefix' => 'fiat'], function () {
                Route::post('fund', [Wallet::class, 'nairaFund']);
                Route::post('fund/complete', [Wallet::class, 'completeFund']);
                Route::post('transfer', [Wallet::class, 'transfer']);
            });
            Route::prefix('crypto')->group(function () {
                Route::post('new-wallet', [Wallet::class, 'newCryptoWallet']);
                Route::post('transfer', [Cryptos::class, 'transfer']);
            });

            Route::group(['prefix' => 'profile'], function () {
                Route::post('/update', [Profiles::class, 'updateProfile']);
                Route::post('/banking-detail/add', [Profiles::class, 'addAccount']);
                Route::post('/change-password', [Profiles::class, 'changePassword']);
                Route::post('/settings', [Profiles::class, 'changeSettings']);
            });

            Route::group(['prefix' => 'bills'], function () {
                Route::post('purchase-airtime', [Bills::class, 'buyAirtime']);
                Route::post('purchase-data', [Bills::class, 'dataPurchase']);
                Route::post('cable-subscription', [Bills::class, 'subscribeCable']);
            });

        });
    });

    
});