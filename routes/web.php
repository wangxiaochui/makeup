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

//Route::get('/', function () {
//    return view('welcome');
//});
//
//Route::get('user', 'UserController@index');
//Route::get('auto', 'AutoController@index');
//Route::get('auto/test', 'AutoController@test');
//
//Route::get('images/{date}/{image}', function ($date, $image) {
//
//    $file = storage_path('app/data/'.$date.'/'.$image);
//    if (file_exists($file)) {
//        return Response::make(File::get($file), 200)->header('Content-Type', File::mimeType($file));
//    } else {
//        abort(404);
//    }
//});



Route::group(['middleware' => ['web']], function () {
    Route::get('makeup','MakeupController@index');
    Route::get('makeup/new','MakeupController@makeupNew');
    Route::get('makeup/test','MakeupController@test');
    Route::get('makeup/waterfall','MakeupController@waterfall');
    Route::get('user','UserController@index');
});