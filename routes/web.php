<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



Route::group(['middleware'=>'auth'],function (){
   Route::get('get_all_user',[\App\Http\Controllers\ChatController::class,'all_user']) ;
   Route::get('auth_id',[\App\Http\Controllers\ChatController::class,'authId']);
   Route::get('/chat',[\App\Http\Controllers\ChatController::class,'index']);
   Route::get('/get_chat',[\App\Http\Controllers\ChatController::class,'get_chat']);
   Route::post('send_message',[\App\Http\Controllers\ChatController::class,'send']);
   Route::post('store_message',[\App\Http\Controllers\ChatController::class,'store']);
});


