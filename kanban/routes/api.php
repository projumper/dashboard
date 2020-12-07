<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('v1/addTask', [TaskController::class, 'addTask'])->name('add');
Route::put('v1/editTask', [TaskController::class, 'editTask'])->name('edit');
Route::get('v1/getAll', [TaskController::class, 'getAll']);
Route::get('v1/getTask', 'Task@getTask');
