<?php

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

Route::prefix('student')
    ->group(function(){
        Route::post('/auth/login',  'AuthController@login');

        // 课程列表
        Route::get('courses',  'StudentController@getCourses');

        Route::middleware([])->group(function(){

        });
    });
