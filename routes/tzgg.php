<?php
/**
 * Created by PhpStorm.
 * User: plyjdz
 * Date: 18-8-3
 * Time: 下午3:27
 */

Route::prefix('tzgg')->middleware('token')->group(function (){
        Route::post('create','TZGG\TZGGController@create');
        Route::post('update','TZGG\TZGGController@update');
        Route::put('publish/{id}','TZGG\TZGGController@publish');
        Route::delete('delete/{id}','TZGG\TZGGController@delete');
        Route::post('file/{id}','TZGG\TZGGController@uploadFile');
});