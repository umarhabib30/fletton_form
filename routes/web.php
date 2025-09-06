<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\UserSurveyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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


Auth::routes();




// admin routes
Route::group(['prefix'=> 'admin'], function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // survey routes
    Route::get('surveys', [SurveyController::class, 'index'])->name('admin.survey.index');
    Route::get('survey/{id}', [SurveyController::class, 'show'])->name('admin.survey.show');
});


// user routes
Route::get('/', [UserSurveyController::class, 'signup'])->name('user.survey.signup');
Route::post('signup/submit', [UserSurveyController::class, 'submitSurvey'])->name('user.survey.submit');
Route::get('flettons-listing-page/{id}', [UserSurveyController::class, 'flettonsListingPage'])->name('user.flettons.listing.page');
Route::post('flettons-listing-page/submit', [UserSurveyController::class, 'submitListingPage'])->name('user.flettons.listing.submit');
Route::get('flettons-rics-survey/{id}', [UserSurveyController::class, 'flettonsRicsSurveyPage'])->name('user.flettons.rics.survey.page');
Route::post('flettons-rics-survey/submit', [UserSurveyController::class, 'submitRicsSurveyPage'])->name('user.flettons.rics.survey.submit');
