<?php

use Illuminate\Support\Facades\Route;

Route::get('verify-email/{user}', 'AuthController@verify')
    ->middleware('signed')
    ->name('auth.verify');

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

Route::get('events/root', 'EventController@getEventsRoot')->middleware('auth:api');

//только для создателя
Route::group(['prefix' => 'events/{event}', 'middleware' => ['auth:api', 'isCreator']], function() {
    Route::delete('', 'EventController@destroy');
    Route::put('', 'EventController@update');
});

Route::group(['prefix' => 'events/{event}', 'middleware' => 'auth:api'], function() {
    Route::get('users', 'EventUserController@show_users_from_event');
    Route::get('visitors', 'VisitedController')
        ->middleware('isCreator');          //только для создателя
    Route::get('test', 'TestController@show')
        ->middleware('isParticipant');      //только для создателя или участника
    Route::post('users', 'EventUserController@add_user');

    //только для создателя или участника, если проверка qr или каптча и нет блокировки
    Route::post('code', 'EventUserController@add_code_user')
        ->middleware(['isParticipant', 'lock', 'isCode']);

    //только для создателя или участника, если проверка тест и нет блокировки
    Route::post('answers', 'EventUserController@add_answers_user')
        ->middleware(['isParticipant', 'lock', 'isTest']);
});



