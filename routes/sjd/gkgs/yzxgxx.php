<?php

Route::prefix('sjd/yzxgxx')->middleware('token')->group(function (){
    Route::get('ywhxx','SJD\GKGS\YZXGXXController@getYwhxx');
    Route::get('qyxx','SJD\GKGS\YZXGXXController@getQyxx');
    Route::get('xqxx','SJD\GKGS\YZXGXXController@getXqxx');
});