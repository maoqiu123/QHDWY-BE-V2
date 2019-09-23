<?php
//业主投诉
Route::prefix('sjd/yzts')->middleware('token')->group(function (){
    Route::post('create','SJD\BMFW\YZTSXXController@createTsxx');

    Route::get('list','SJD\BMFW\YZTSXXController@getTsxxList');
    Route::get('one/{id}','SJD\BMFW\YZTSXXController@getTsxx');

    Route::post('eva/{id}','SJD\BMFW\YZTSXXController@evaluateTsxx');
    Route::delete('recall/{id}','SJD\BMFW\YZTSXXController@recallTsxx');

    Route::post('file/{id}','SJD\BMFW\YZTSXXController@uploadFile');
    Route::delete('file', 'SJD\BMFW\YZTSXXController@deleteFile');
});