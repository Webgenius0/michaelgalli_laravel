<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\CarbController;
use App\Http\Controllers\Web\Backend\ChatController;
use App\Http\Controllers\Web\Backend\PageController;
use App\Http\Controllers\Web\Backend\PostController;
use App\Http\Controllers\Web\Backend\ImageController;
use App\Http\Controllers\Web\Backend\RecipeController;
use App\Http\Controllers\Web\Backend\ContactController;
use App\Http\Controllers\Web\Backend\CuisineController;
use App\Http\Controllers\Web\Backend\ProteinController;
use App\Http\Controllers\Web\Backend\CaloriesController;
use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Web\Backend\MealPlanController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\HealthGoalController;
use App\Http\Controllers\Web\Backend\SocialLinkController;
use App\Http\Controllers\Web\Backend\SubscriberController;
use App\Http\Controllers\Web\Backend\Access\RoleController;
use App\Http\Controllers\Web\Backend\Access\UserController;
use App\Http\Controllers\Web\Backend\SubcategoryController;
use App\Http\Controllers\Web\Backend\TimeToClockController;
use App\Http\Controllers\Web\Backend\WeeklyRecipeController;
use App\Http\Controllers\Web\Backend\MealPlanOptionController;
use App\Http\Controllers\Web\Backend\Settings\OtherController;
use App\Http\Controllers\Web\Backend\Settings\SocialController;
use App\Http\Controllers\Web\Backend\Settings\StripeController;
use App\Http\Controllers\Web\Backend\Settings\CaptchaController;
use App\Http\Controllers\Web\Backend\Settings\ProfileController;
use App\Http\Controllers\Web\Backend\Settings\SettingController;
use App\Http\Controllers\Web\Backend\Access\PermissionController;
use App\Http\Controllers\Web\Backend\Settings\FirebaseController;
use App\Http\Controllers\Web\Backend\Settings\GoogleMapController;
use App\Http\Controllers\Web\Backend\SubscriptionFeatureController;
use App\Http\Controllers\Web\Backend\Settings\MailSettingController;
use App\Http\Controllers\Web\Backend\CMS\Web\Home\HomeBannerController;
use App\Http\Controllers\Web\Backend\CMS\Web\Home\HomeHowToWorkController;
use App\Http\Controllers\Web\Backend\CMS\Web\Home\HomePersonalizedController;
use App\Http\Controllers\Web\Backend\CustomerReviewController;

Route::get("dashboard", [DashboardController::class, 'index'])->name('dashboard');


/*
* CRUD
*/

Route::controller(CategoryController::class)->prefix('category')->name('category.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});


Route::controller(ProteinController::class)->prefix('protein')->name('protein.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});


Route::controller(CaloriesController::class)->prefix('calories')->name('calories.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});


Route::controller(CarbController::class)->prefix('carb')->name('carb.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});


Route::controller(CuisineController::class)->prefix('cuisine')->name('cuisine.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});


Route::controller(HealthGoalController::class)->prefix('health_goal')->name('health_goal.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});

Route::controller(TimeToClockController::class)->prefix('time_to_clock')->name('time_to_clock.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});







Route::controller(SubcategoryController::class)->prefix('subcategory')->name('subcategory.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});

Route::controller(RecipeController::class)->prefix('recipe')->name('recipe.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});



Route::controller(CustomerReviewController::class)->prefix('review')->name('review.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});








Route::controller(WeeklyRecipeController::class)->prefix('weekly-recipe')->name('weekly_recipe.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::get('/status/{id}', 'status')->name('status');


    Route::post('/recipe/add/{week_id}', 'add_recipe')->name('recipe.recipe_add');
    Route::delete('/recipe/delete/{id}', 'destroy')->name('recipe.delete');

});



Route::controller(MealPlanController::class)->prefix('meal-plan')->name('meal_plan.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});


Route::controller(MealPlanController::class)->prefix('meal-plan')->name('meal_plan.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});


Route::controller(MealPlanOptionController::class)->prefix('meal-plan-options')->name('meal_plan_option.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});




Route::controller(SubscriptionFeatureController::class)->prefix('feature')->name('feature.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');

});





Route::controller(PostController::class)->prefix('post')->name('post.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});

Route::controller(ImageController::class)->prefix('post/image')->name('post.image.')->group(function () {
    Route::get('/{post_id}', 'index')->name('index');
    Route::get('/delete/{id}', 'destroy')->name('destroy');
});

Route::controller(PageController::class)->prefix('page')->name('page.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});

Route::controller(SocialLinkController::class)->prefix('social')->name('social.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});

Route::controller(FaqController::class)->prefix('faq')->name('faq.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');
    Route::get('/status/{id}', 'status')->name('status');
});

Route::get('subscriber', [SubscriberController::class, 'index'])->name('subscriber.index');

Route::controller(ContactController::class)->prefix('contact')->name('contact.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/status/{id}', 'status')->name('status');
});


/*
* CMS
*/

Route::prefix('cms')->name('cms.')->group(function () {

    //Home Banner
    Route::prefix('home/banner')->name('home.banner.')->controller(HomeBannerController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::patch('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/{id}/status', 'status')->name('status');

        Route::put('/content', 'content')->name('content');
    });

    //Home Banner
    Route::prefix('home/how_it_work')->name('home.how_it_work.')->controller(HomeHowToWorkController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::patch('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/{id}/status', 'status')->name('status');

        Route::put('/content', 'content')->name('content');
    });


    Route::prefix('home/personalized')->name('home.personalized.')->controller(HomePersonalizedController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::patch('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/{id}/status', 'status')->name('status');

        Route::put('/content', 'content')->name('content');
    });

});

/*
* Chating Route
*/

Route::controller(ChatController::class)->prefix('chat')->name('chat.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/list', 'list')->name('list');
    Route::post('/send/{receiver_id}', 'send')->name('send');
    Route::get('/conversation/{receiver_id}', 'conversation')->name('conversation');
    Route::get('/room/{receiver_id}', 'room');
    Route::get('/search', 'search')->name('search');
    Route::get('/seen/all/{receiver_id}', 'seenAll');
    Route::get('/seen/single/{chat_id}', 'seenSingle');
});


/*
* Users Access Route
*/

Route::resource('users', UserController::class);
Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
    Route::get('/status/{id}', 'status')->name('status');
    Route::get('/new', 'new')->name('new.index');
    Route::get('/ajax/new/count', 'newCount')->name('ajax.new.count');
});
Route::resource('permissions', PermissionController::class);
Route::resource('roles', RoleController::class);

/*
*settings
*/

// Route for Profile Settings
Route::controller(ProfileController::class)->group(function () {
    Route::get('setting/profile', 'index')->name('setting.profile.index');
    Route::put('setting/profile/update', 'UpdateProfile')->name('setting.profile.update');
    Route::put('setting/profile/update/Password', 'UpdatePassword')->name('setting.profile.update.Password');
    Route::post('setting/profile/update/Picture', 'UpdateProfilePicture')->name('update.profile.picture');
});

// Route for Mail Settings
Route::controller(MailSettingController::class)->group(function () {
    Route::get('setting/mail', 'index')->name('setting.mail.index');
    Route::patch('setting/mail', 'update')->name('setting.mail.update');
});

// Route for Stripe Settings
Route::controller(StripeController::class)->prefix('setting/stripe')->name('setting.stripe.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::patch('/update', 'update')->name('update');
});

// Route for Firebase Settings
Route::controller(FirebaseController::class)->prefix('setting/firebase')->name('setting.firebase.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::patch('/update', 'update')->name('update');
});

// Route for Firebase Settings
Route::controller(SocialController::class)->prefix('setting/social')->name('setting.social.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::patch('/update', 'update')->name('update');
});

// Route for Stripe Settings
Route::controller(SettingController::class)->group(function () {
    Route::get('setting/general', 'index')->name('setting.general.index');
    Route::patch('setting/general', 'update')->name('setting.general.update');
});

// Route for Google Map Settings
Route::controller(GoogleMapController::class)->group(function () {
    Route::get('setting/google/map', 'index')->name('setting.google.map.index');
    Route::patch('setting/google/map', 'update')->name('setting.google.map.update');
});

// Route for Google Map Settings
Route::controller(CaptchaController::class)->group(function () {
    Route::get('setting/captcha', 'index')->name('setting.captcha.index');
    Route::patch('setting/captcha', 'update')->name('setting.captcha.update');
});

//Ajax settings
Route::prefix('setting/other')->name('setting.other')->group(function () {
    Route::get('/', [OtherController::class, 'index'])->name('.index');
    Route::get('/mail', [OtherController::class, 'mail'])->name('.mail');
    Route::get('/sms', [OtherController::class, 'sms'])->name('.sms');
    Route::get('/recaptcha', [OtherController::class, 'recaptcha'])->name('.recaptcha');
    Route::get('/pagination', [OtherController::class, 'pagination'])->name('.pagination');
    Route::get('/reverb', [OtherController::class, 'reverb'])->name('.reverb');
});
