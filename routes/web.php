<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PersonalAccessTokensController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|generate.token
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['middleware'=>'PreventBackHistory'])->group(function(){
    Auth::routes();
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Routes for admin and user login 
Route::group(['prefix'=>'admin', 'middleware'=>['isAdmin','auth','PreventBackHistory']], function(){
    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('profile', [AdminController::class, 'profile'])->name('admin.profile');
    
    Route::resource('categories','CategoriesController');

    //Routes for create Plan
    Route::get('plans-list', 'PlanController@index')->name('plans.list');
    Route::get('create/plan', 'SubscriptionController@createPlan')->name('create.plan');
    Route::post('store/plan', 'SubscriptionController@storePlan')->name('store.plan');
});

Route::group(['prefix'=>'user', 'middleware'=>['isUser','auth','PreventBackHistory']], function(){
    Route::get('dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('profile', [UserController::class, 'profile'])->name('user.profile');

    Route::post('generate/token', [PersonalAccessTokensController::class, 'generate_token'])->name('generate.token');
    
    Route::get('plans', 'PlanController@index')->name('plans.index');
    Route::get('plan/{plan}', 'PlanController@show')->name('plans.show');
    Route::post('subscription', 'SubscriptionController@create')->name('subscription.create');
    
    //Routes for subscription plans
    Route::get('/subscribe', 'SubscriptionController@showSubscription')->name('plans.showSubscription');
    Route::post('/subscribe', 'SubscriptionController@processSubscription')->name('plans.processSubscription');
});
