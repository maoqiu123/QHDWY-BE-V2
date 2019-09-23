<?php
//业主报修
Route::prefix('sjd/yzbx')->middleware('token')->group(function (){
    Route::post('create','SJD\BMFW\YZBXXXController@createBxxx');

    Route::get('list','SJD\BMFW\YZBXXXController@getBxxxList');
    Route::get('one/{id}','SJD\BMFW\YZBXXXController@getBxxx');

    Route::post('eva/{id}','SJD\BMFW\YZBXXXController@evaluateBxxx');
    Route::delete('recall/{id}','SJD\BMFW\YZBXXXController@recallBxxx');

    Route::post('file/{id}','SJD\BMFW\YZBXXXController@uploadFile');
    Route::delete('file', 'SJD\BMFW\YZBXXXController@deleteFile');
});