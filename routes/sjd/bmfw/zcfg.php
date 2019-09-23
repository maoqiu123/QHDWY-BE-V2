<?php
//政策法规
Route::prefix('sjd/zcfg')->middleware('token')->group(function (){
    Route::get('search','SJD\BMFW\ZCFGController@searchZcfg');
    Route::get('one/{id}','SJD\BMFW\ZCFGController@getZcfgInfo');
});