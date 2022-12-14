<?php

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

use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Http\Middleware\AdminLog;
use App\Http\Middleware\CheckIpBlacklist;

Route::prefix(cnpscy_config('admin_prefix'))
    ->middleware([CheckIpBlacklist::class, AdminLog::class])
    ->group(function() {
//    Route::get('/', 'AdminController@index');
    //后台管理路由
    Route::get('/', function(){
        return view('admin::admin');
    });
});
