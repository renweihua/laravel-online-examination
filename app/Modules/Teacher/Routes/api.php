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

Route::prefix('teacher')
    ->group(function(){
    Route::post('/auth/login',  'AuthController@login');

    Route::middleware([])->group(function(){
        // Vue格式的课程列表
        Route::get('vue-courses',  'TeacherController@getFormatCoursesByVue');

        // 试卷管理
        Route::prefix('paper')->group(function(){
            Route::post('create',  'PaperController@create');
        });
        // 题库管理
        Route::prefix('question_bank')->group(function(){
            Route::get('',  'QuestionBankController@index');
            Route::post('create',  'QuestionBankController@createOrUpdate');
            Route::put('update',  'QuestionBankController@createOrUpdate');
            Route::delete('delete',  'QuestionBankController@delete');
        });
    });
});
