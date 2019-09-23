<?php

Route::post('/fgj/generate','UserController@generateFgj')->middleware('token');
Route::post('/user/login','UserController@login');
Route::get('/user/info','UserController@getUserInfo');
Route::get('/user/judge','UserController@isTokenExpired')->middleware('token');
Route::post('/user/reset','UserController@resetPassword')->middleware('token');