<?php

use Illuminate\Support\Facades\Route;


Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout')->middleware('auth:api');

Route::group(['prefix' => 'users', 'middleware' => 'auth:api'], function() {
    Route::get('', 'UserController@show');
    Route::put('', 'UserController@update');
    Route::delete('', 'UserController@destroy');
});

Route::apiResource('events','EventController')->middleware('auth:api');
Route::group(['prefix' => 'events', 'middleware' => 'auth:api'], function() {
    Route::get('{event}/users', 'EventUserController@show_users_from_event');
    Route::get('{event}/visitors', 'VisitedController');
    Route::get('{event}/test', 'TestController@show');
    Route::post('{event}/users', 'EventUserController@add_user');
    Route::post('{event}/code', 'EventUserController@add_code_user');
    Route::post('{event}/answers', 'EventUserController@add_answers_user');
});



