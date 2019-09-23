<?php

Route::prefix('jsdw')->middleware('token')->group(function (){

    Route::prefix('jbxx')->group(function (){
        Route::get('/getbytoken','JSDW\JSDWJBXXController@getByToken');
        Route::post('/save','JSDW\JSDWJBXXController@update');
    });

    Route::prefix('zlyj')->group(function (){
        Route::get('search','JSDW\JSDWZLYJController@search');
        Route::post('create','JSDW\JSDWZLYJController@create');
        Route::post('update/{zlyjId}','JSDW\JSDWZLYJController@update');
        Route::get('xxxx/{zlyjId}','JSDW\JSDWZLYJController@showById');
        Route::delete('file', 'JSDW\JSDWZLYJController@deleteFile');
        Route::delete('{zlyjId}','JSDW\JSDWZLYJController@delete');
        Route::get('submit/{zlyjId}','JSDW\JSDWZLYJController@submit');

        Route::post('file/{id}','JSDW\JSDWZLYJController@uploadFile');

    });

});