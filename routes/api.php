<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\Frontend\categoryController;
use App\Http\Controllers\Api\Frontend\DeliveryAddressController;
use App\Http\Controllers\Api\Frontend\FaqController;
use App\Http\Controllers\Api\Frontend\HomeController;
use App\Http\Controllers\Api\Frontend\ImageController;
use App\Http\Controllers\Api\Frontend\PostController;
use App\Http\Controllers\Api\Frontend\QuestionController;
use App\Http\Controllers\Api\Frontend\RecipeCardController;
use App\Http\Controllers\Api\Frontend\RecipeFilterController;
use App\Http\Controllers\Api\Frontend\RecipeManageController;
use App\Http\Controllers\Api\Frontend\SettingsController;
use App\Http\Controllers\Api\Frontend\SocialLinksController;
use App\Http\Controllers\Api\Frontend\StripeWebhookController;
use App\Http\Controllers\Api\Frontend\SubcategoryController;
use App\Http\Controllers\Api\Frontend\SubscriberController;
use App\Http\Controllers\Api\Frontend\SubscriptionController;
use App\Http\Controllers\Api\Frontend\UserDnaReportController;
use App\Http\Controllers\Api\Frontend\UserFamilyMemberController;
use App\Http\Controllers\Api\Frontend\UserProfileController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

//page
Route::get('/page/home', [HomeController::class, 'index']);

Route::get('/category', [categoryController::class, 'index']);
Route::get('/subcategory', [SubcategoryController::class, 'index']);

Route::get('/social/links', [SocialLinksController::class, 'index']);
Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/faq', [FaqController::class, 'index']);
Route::post('subscriber/store', [SubscriberController::class, 'store'])->name('subscriber.store');

Route::get('/question/list', [QuestionController::class, 'index']);

Route::get('/recipe/list', [RecipeManageController::class, 'recipe_list']);
Route::get('/recipe/details/{id}', [RecipeManageController::class, 'recipe_details']);

Route::get('/category/list', [RecipeFilterController::class, 'category_list']);
Route::get('/protein/list', [RecipeFilterController::class, 'protein_list']);
// calories, carbs, cuisine , health_goal, time_to_cook
Route::get('/calories/list', [RecipeFilterController::class, 'calories_list']);
Route::get('/carbs/list', [RecipeFilterController::class, 'carbs_list']);
Route::get('/cuisine/list', [RecipeFilterController::class, 'cuisine_list']);
Route::get('/health_goal/list', [RecipeFilterController::class, 'health_goal_list']);
Route::get('/time_to_cook/list', [RecipeFilterController::class, 'time_to_cook_list']);

/*
# Post
*/
Route::middleware(['auth:api'])->controller(PostController::class)->prefix('auth/post')->group(function () {
    Route::get('/', 'index');
    Route::post('/store', 'store');
    Route::get('/show/{id}', 'show');
    Route::post('/update/{id}', 'update');
    Route::delete('/delete/{id}', 'destroy');
});

Route::get('/posts', [PostController::class, 'posts']);
Route::get('/post/show/{post_id}', [PostController::class, 'post']);

Route::middleware(['auth:api'])->controller(ImageController::class)->prefix('auth/post/image')->group(function () {
    Route::get('/', 'index');
    Route::post('/store', 'store');
    Route::get('/delete/{id}', 'destroy');
});

/*
# Auth Route
*/

Route::group(['middleware' => 'guest:api'], function ($router) {
    //register
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('/verify-email', [RegisterController::class, 'VerifyEmail']);
    Route::post('/resend-otp', [RegisterController::class, 'ResendOtp']);
    Route::post('/verify-otp', [RegisterController::class, 'VerifyEmail']);
    //login
    Route::post('login', [LoginController::class, 'login'])->name('login');
    //forgot password
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/password-verify-otp', [ResetPasswordController::class, 'MakeOtpToken']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
    //social login
    Route::post('/social-login', [SocialLoginController::class, 'SocialLogin']);
});

Route::group(['middleware' => ['auth:api', 'api-otp']], function ($router) {
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/account/switch', [UserController::class, 'accountSwitch']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/update-avatar', [UserController::class, 'updateAvatar']);
    Route::delete('/delete-profile', [UserController::class, 'destroy']);
});

Route::middleware(['auth:api'])->controller(QuestionController::class)->prefix('question')->group(function () {
    Route::post('/store', 'store');
});

// Delivery Address
Route::middleware(['auth:api'])->controller(DeliveryAddressController::class)->prefix('delivery/address')->group(function () {
    Route::get('/list', 'index');
    Route::post('/store', 'store');
});

// user profile
Route::middleware(['auth:api'])->controller(UserProfileController::class)->prefix('user/profile')->group(function () {
    Route::get('/show', 'index');
    Route::post('/update', 'update');
    Route::post('/avatar/update', 'profile_update');

    // change password
    Route::post('/change-password', 'changePassword');
});

// user family member
Route::middleware(['auth:api'])->controller(UserFamilyMemberController::class)->prefix('user/family')->group(function () {
    Route::get('/list', 'familyList');
    Route::post('/store', 'familyStore');
    Route::get('/edit/{id}', 'familyEdit');
    Route::post('/update/{id}', 'familyUpdate');
    Route::delete('/delete/{id}', 'familyDelete');

    // quiz
    Route::post('/quiz/store', 'quizStore');
});

// user family member
Route::middleware(['auth:api'])->controller(RecipeCardController::class)->prefix('user/recipe/card')->group(function () {
    Route::get('/list', 'recipe_list');
    Route::get('/details/{recipe_id}', 'recipe_details');

    // order list
    Route::get('/order/history','order_history');

});

Route::get('/download-pdf/{id}', [RecipeCardController::class, 'download_recipe_pdf'])->name('api.recipe.download');

// user family member
Route::middleware(['auth:api'])->controller(UserDnaReportController::class)->prefix('user/dna/report')->group(function () {

    Route::post('/store', 'store');

});

// subscribe
Route::middleware(['auth:api'])->controller(SubscriptionController::class)->prefix('subscriber')->group(function () {


    // create , pause, cancel

    Route::post('/package/recipe/add-to-cart', 'add_to_cart');


    Route::post('/subscribe', 'subscribe');
    Route::post('/pause', 'pauseSubscription');
    Route::post('/cancel', 'cancelSubscription');

    // get subscription details
    Route::get('/details', 'subscriptionDetails');
});

// stripe webhook
Route::post('/orders', [StripeWebhookController::class, 'orderIngredient'])->middleware('auth:api');
Route::get('/subscriber/meal/plans', [SubscriptionController::class, 'mealPlans']);

/*
# Firebase Notification Route
*/

Route::middleware(['auth:api'])->controller(FirebaseTokenController::class)->prefix('firebase')->group(function () {
    Route::get("test", "test");
    Route::post("token/add", "store");
    Route::post("token/get", "getToken");
    Route::post("token/delete", "deleteToken");
})->middleware('auth:api');

/*
# In App Notification Route
*/

Route::middleware(['auth:api'])->controller(NotificationController::class)->prefix('notify')->group(function () {
    Route::get('test', 'test');
    Route::get('/', 'index');
    Route::get('status/read/all', 'readAll');
    Route::get('status/read/{id}', 'readSingle');





})->middleware('auth:api');


Route::post('/notify/contact/us', [NotificationController::class, 'contact_us']);

/*
# Chat Route
*/

Route::middleware(['auth:api'])->controller(ChatController::class)->prefix('auth/chat')->group(function () {
    Route::get('/list', 'list');
    Route::post('/send/{receiver_id}', 'send');
    Route::get('/conversation/{receiver_id}', 'conversation');
    Route::get('/room/{receiver_id}', 'room');
    Route::get('/search', 'search');
    Route::get('/seen/all/{receiver_id}', 'seenAll');
    Route::get('/seen/single/{chat_id}', 'seenSingle');
});

/*
# CMS
*/

Route::prefix('cms')->name('cms.')->group(function () {
    Route::get('home/how-it-works', [HomeController::class, 'index'])->name('home');
});
