<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['localization']], function () {

    // Countries & States & Cities
    Route::apiResource('country', 'CountryController', ['only' => ['index', 'show']]);
    Route::get('/get-country-id', 'CountryController@getCountryId');
    Route::apiResource('state', 'StateController', ['only' => ['index', 'show']]);
    Route::get('/get-states/{country_id}', 'StateController@getStates');
    Route::apiResource('city', 'CityController', ['only' => ['index', 'show']]);
    Route::get('/get-cities/{state_id}', 'CityController@getCities');

    // Authentication
    Route::post('/login', 'AuthController@login');
    Route::post('/register', 'AuthController@register');
    Route::post('/verify-register', 'AuthController@verifyRegister');
    ### AWS Amazon Rekognition  [App\Http\Controllers\RekognitionController::class, 'verifyIdentity']
    Route::post('/verify-identity', 'AwsRekognitionController@verifyIdentity');

    Route::post('/resend-registration-otp', 'AuthController@resendRegistrationOtp');
    Route::get('/get-sms-methods', 'AuthController@getAllSMSMethods');
    Route::post('/verify-token', 'AuthController@verifyToken');
    Route::post('/forgot-password', 'AuthController@forgotPassword');
    Route::post('/resend-password-reset-otp', 'AuthController@resendPasswordResetOtp');
    Route::post('/update-password', 'AuthController@updatePassword');
    Route::post('/verify-otp', 'AuthController@verify_auth_token');
    Route::post('/check-validation', 'AuthController@checkUserValidation');
    Route::post('/logout', 'AuthController@logout');

    // Settings
    Route::get('settings', 'SettingController@index');

    // Currencies
    Route::apiResource('currency', 'CurrencyController', ['only' => ['index', 'show']]);

    // Languages
    Route::apiResource('language', 'LanguageController', ['only' => ['index', 'show']]);
    Route::get('translate/{file}', 'LanguageController@translate');

    // Pages
    Route::apiResource('page', 'PageController', ['only' => ['index', 'show']]);
    Route::get('page/slug/{slug}', 'PageController@getPagesBySlug');

    // Categories
    Route::apiResource('category', 'CategoryController', ['only' => ['index', 'show']]);
    Route::get('category/slug/{slug}', 'CategoryController@getCategoryBySlug');

    // Tags
    Route::apiResource('tag', 'TagController', ['only' => ['index', 'show']]);

    // Blogs
    Route::apiResource('blog', 'BlogController', ['only' => ['index', 'show']]);
    Route::get('blog/slug/{slug}', 'BlogController@getBlogBySlug');

    Route::group(['middleware' => ['auth:sanctum'], 'as' => 'api.'], function () {

        // Account
        Route::get('self', 'AccountController@self');
        Route::put('update-profile', 'AccountController@updateProfile');
        Route::put('updatePassword', 'AccountController@updatePassword');
        Route::delete('deleteAccount', 'AccountController@deleteAccount');
        Route::put('activateNotification', 'AccountController@activateNotification');

        // Notifications
        Route::get('notifications', 'NotificationController@index');
        Route::delete('notifications/{id}', 'NotificationController@destroy');
        Route::put('notifications/markAsRead', 'NotificationController@markAsRead');

        // FAQs
        Route::get('faqs', 'FaqController@index');
        // FAQs Categories
        Route::get('faq-categories', 'FaqController@categories');

        // Payment Methods
        Route::get('payment-methods', 'PaymentMethodController@index');
    });
});

