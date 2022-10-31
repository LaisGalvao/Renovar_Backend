<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\RegisterController;
use App\Http\Controllers\api\UsersController;
use App\Http\Controllers\api\UserController;

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

/*
Route::middleware('auth:api')->get('/users', function (Request $request) {
    return $request->user();
});


Route::middleware('api')->get('/registers', function (Request $request) {
    return [];

});

Route::get('/ok', function () {
    return ['funciona'];
});

Route::get('users/', function (Request $request) {
    //return[];
     return $request->user();
});

Route::get('users/{id}', function ($id) {
    return $id;

});
*/

     //users
    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/users/{id}', [UsersController::class, 'show']);
    Route::post('/users', [UsersController::class, 'store']);

//register
Route::get('register', [RegisterController::class, 'index']);
Route::get('register/{id}', [UsersController::class, 'show']);
Route::post('register', [RegisterController::class, 'store']);

//user (para testar autenticação)
Route::post('regist', [UserController::class, 'regist']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'user']);
    Route::get('/logout', [UserController::class, 'logout']);
});
