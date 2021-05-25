<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonalAccessTokensController;

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('categories','CategoriesController');

Route::post('generate/token', [PersonalAccessTokensController::class, 'generate_token'])->name('generate.token');

//Routes for subscription plans
Route::get('/plans', 'PlanController@index')->name('plans.index');
Route::get('/plan/{plan}', 'PlanController@show')->name('plans.show');
Route::post('/subscription', 'SubscriptionController@create')->name('subscription.create');

//Routes for create Plan
Route::get('create/plan', 'SubscriptionController@createPlan')->name('create.plan');
Route::post('store/plan', 'SubscriptionController@storePlan')->name('store.plan');
