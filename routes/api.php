<?php

use App\Http\Controllers\Api\V1\AnswerController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ChallengeController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DateideaController;
use App\Http\Controllers\Api\V1\DiaryController;
use App\Http\Controllers\Api\V1\GiftController;
use App\Http\Controllers\Api\V1\LoveLanguagesController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\PairController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ValueController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




Route::prefix('v1')->group(function () {

    //Protected Routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::post('/values', [ValueController::class, 'store']);
        Route::get('/prefs', [ValueController::class, 'prefs']);
        Route::get('/dateideas/{type}', [DateideaController::class, 'get']);
        Route::get('/rules', [ValueController::class, 'rules']);
        Route::post('/rules', [ValueController::class, 'storeRule']);
        Route::get('/lovelanguages/results', [LoveLanguagesController::class, 'get']);
        Route::get('/lovelanguages', [LoveLanguagesController::class, 'getQuestionsWithAnswers']);
        Route::post('/lovelanguages', [LoveLanguagesController::class, 'saveLoveLanguagesAnswers']);
        Route::post('/lovelanguages/result', [LoveLanguagesController::class, 'saveLoveLanguagesResult']);
        Route::put('/lovelanguages/result', [LoveLanguagesController::class, 'updateLoveLanguagesResult']);
        Route::get('/dashboard', [DashboardController::class, 'get']);
        Route::get('/notes', [NoteController::class, 'index']);
        Route::delete('/notes/{id}', [NoteController::class, 'destroy']);
        Route::post('/notes', [NoteController::class, 'store']);
        Route::get('/questions/{type}', [QuestionController::class, 'get']);
        Route::post('/answer', [AnswerController::class, 'store']);
        Route::post('/dateidea/{id}', [DateideaController::class, 'update']);
        Route::get('/diary', [DiaryController::class, 'index']);
        Route::post('/diary', [DiaryController::class, 'store']);
        Route::get('/gifts', [GiftController::class, 'get']);
        Route::post('/gifts', [GiftController::class, 'addGift']);

        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index']); // Alle Abonnements anzeigen
            Route::post('/', [SubscriptionController::class, 'store']); // Neues Abonnement erstellen
            Route::get('{id}', [SubscriptionController::class, 'show']); // Ein Abonnement anzeigen
            Route::put('{id}/status', [SubscriptionController::class, 'updateStatus']); // Status eines Abonnements aktualisieren
            Route::delete('{id}', [SubscriptionController::class, 'destroy']); // Abonnement löschen
        });

        Route::prefix('pair')->group(function () {
            Route::get('/', [PairController::class, 'getOurData']); 
            Route::get('/{uuid}', [PairController::class, 'get']);
            Route::put('/anniversary', [PairController::class, 'updateAnniversary']); 
            Route::post('/connect', [PairController::class, 'connect']); 
        });
        //Route::get('/commonprefs', [ValueController::class, 'commonprefs']); 
        // Route::get('/commonrules', [ValueController::class, 'commonrules']);

        Route::post('/challenge', [ChallengeController::class, 'close']); 

    });

    //Public Routes 
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/native', [AuthController::class, 'loginnative']);
    Route::post('/checkGroupId', [AuthController::class, 'checkGroupId']);
    Route::post('/checkemail', [AuthController::class, 'checkEmail']);

    //diese Urls müssen später in protected routes

   
    Route::post('/register', [UserController::class, 'register']);
    
   
    Route::get('/send', [UserController::class, 'pushnotification']);
});
