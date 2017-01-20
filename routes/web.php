<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::get('makeup','MakeupController@index');
    Route::get('makeup/new','MakeupController@makeupNew');
    Route::get('makeup/test','MakeupController@test');
    Route::get('makeup/hctemp','MakeupController@hcTemp');
    Route::get('makeup/waterfall','MakeupController@waterfall');
    Route::get('makeup/wxbook','MakeupController@wxbook');
    Route::get('user','UserController@index');
    Route::get('auto', 'AutoController@index');
    Route::get('auto/test', 'AutoController@test');
});