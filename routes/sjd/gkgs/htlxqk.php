<?php
//合同履行情况
Route::prefix('sjd/htlxqk')->middleware('token')->group(function (){
    Route::get('search','SJD\GKGS\WYHTLXGSController@getWyhtlxqk');
    Route::get('detail','SJD\GKGS\WYHTLXGSController@getDetail');
});