<?php

Route::prefix('sjd/ywhgzjl')->middleware('token')->group(function (){
    Route::get('search','SJD\GKGS\YWHGZJLGSController@getYwhgzjl');
    Route::get('detail','SJD\GKGS\YWHGZJLGSController@getDetail');
});