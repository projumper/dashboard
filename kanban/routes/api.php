<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeHourController;
use App\Http\Controllers\TaskDetailInfoController;

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
    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });
    */


    Route::post('v1/addTask', [TaskController::class, 'addTask'])->name('add');
    Route::put('v1/editTask', [TaskController::class, 'editTask'])->name('edit');
    Route::get('v1/getAll', [TaskController::class, 'getAll']);
    Route::get('v1/getTask', 'Task@getTask');

    Route::put('v1/deleteTaskData', [TaskController::class, 'deleteTaskData'])->name('deletetaskdata');


    Route::get('v1/getworklog/key/{key}', [TaskController::class, 'getWorklog'])->name('getworklog');

    Route::get('v1/gettaskdata/key/{key}', [TaskController::class, 'getTaskData'])->name('gettaskdata');

    Route::get('v1/get/user/{user}/date/{date}/status/{status}', [EmployeeHourController::class, 'getTime'])->name('gettime');



    Route::post('v1/addTime', [EmployeeHourController::class, 'addTime'])->name('addtime');

    Route::get('v1/getEmployeeTime/date/{date}/{project?}', [EmployeeHourController::class, 'getEmployeeTime'])->name('getemployeetime');

    Route::get('v1/getTasks/date/{date}', [TaskController::class, 'getTasksDate'])->name('gettasks');

    Route::get('v1/getEmployeeWeekPlan/week/{date}/{project?}', [TaskDetailInfoController::class, 'getEmployeeWeekPlan'])->name('getthisweek');

    Route::get('v1/getOpenTasks/{project?}',[TaskDetailInfoController::class, 'getOpenTasks'])->name('getopentasks');

    Route::get('v1/getMonthData/date/{date}/{project?}',[TaskDetailInfoController::class, 'getMonthData'])->name('getmonthdata');

    //Route::get('v1/get/user/{user}/date/{date}/status/{status}', [EmployeeHourController::class, 'getTime'])->name('gettime');
