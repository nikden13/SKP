<?php

use Illuminate\Support\Facades\Route;


Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout')
    ->middleware('auth:api');

Route::group(['prefix' => 'users', 'middleware' => 'auth:api'], function() {
    Route::get('', 'UserController@show');
    Route::put('', 'UserController@update');
    Route::delete('', 'UserController@destroy');
});

Route::apiResource('events','EventController')
    ->except('destroy', 'create')
    ->middleware('auth:api');

//только для создателя
Route::group(['prefix' => 'events/{event}', 'middleware' => ['auth:api', 'isCreator']], function() {
    Route::delete('', 'EventController@destroy');
    Route::put('', 'EventController@udpate');
});

Route::group(['prefix' => 'events/{event}', 'middleware' => 'auth:api'], function() {
    Route::get('users', 'EventUserController@show_users_from_event');
    Route::get('visitors', 'VisitedController')
        ->middleware('isCreator');          //только для создателя
    Route::get('test', 'TestController@show')
        ->middleware('isParticipant');      //только для создателя или участника
    Route::post('users', 'EventUserController@add_user');

    //только для создателя или участника и если проверка qr или каптча
    Route::post('code', 'EventUserController@add_code_user')
        ->middleware(['isCreator', 'isCode']);

    //только для создателя или участника и если проверка тест
    Route::post('answers', 'EventUserController@add_answers_user')
        ->middleware(['isCreator','isTest']);
});



