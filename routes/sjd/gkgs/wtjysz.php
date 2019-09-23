<?php
//委托经营收支
Route::prefix('sjd/wtjysz')->middleware('token')->group(function (){
    Route::get('search','SJD\GKGS\WTJYGSController@getWtjy');
    Route::get('detail','SJD\GKGS\WTJYGSController@getDetail');
});