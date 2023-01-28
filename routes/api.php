<?php

use App\Http\Controllers\Api\CrawlerController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/crawler/start', [CrawlerController::class,'start']);


Route::get('/url', [WebhookController::class,'index']);
Route::post('/url/add', [WebhookController::class,'store']);
Route::delete('/url/remove', [WebhookController::class,'remove']);
