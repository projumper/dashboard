<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
});


Route::get('/matime', function () {
    return view('matime');
});



Route::get('/getthisweek', function () {
    return view('getthisweek');
});


Route::get('/alltasks', function () {
    return view('alltasks');
});

Route::get('/getemployeetime', function () {
    return view('getemployeetime');
});

Route::get('/getopentask', function () {
    return view('getopentask');
});


Route::get('/getmonthdata', function () {
    return view('getmonthdata');
});

