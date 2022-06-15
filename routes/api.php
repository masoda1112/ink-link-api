<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ItemController;

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
Route::post('v1/users', [UserController::class,"create"]);
Route::get('v1/users/{user_id}', [UserController::class,"show"])->middleware(['auth']);
Route::put('v1/users/{user_id}', [UserController::class,"update"])->middleware(['auth']);
Route::put('v1/live_rooms/join', [RoomController::class,"join"])->middleware(['auth']);
Route::put('v1/live_rooms/leave', [RoomController::class,"leave"])->middleware(['auth']);
Route::put('v1/gachas/pull', [RoomController::class,"selectItem"])->middleware(['auth']);
Route::post('v1/gachas', [ItemController::class,"create"]);
