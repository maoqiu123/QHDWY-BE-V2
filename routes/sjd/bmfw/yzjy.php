<?php
//业主建议
Route::prefix('sjd/yzjy')->middleware('token')->group(function (){
    Route::post('create','SJD\BMFW\YZJYXXController@createJyxx');

    Route::get('list','SJD\BMFW\YZJYXXController@getJyxxList');
    Route::get('one/{id}','SJD\BMFW\YZJYXXController@getJyxx');

    Route::post('eva/{id}','SJD\BMFW\YZJYXXController@evaluateJyxx');
    Route::delete('recall/{id}','SJD\BMFW\YZJYXXController@recallJyxx');

    Route::post('file/{id}','SJD\BMFW\YZJYXXController@uploadFile');
    Route::delete('file', 'SJD\BMFW\YZJYXXController@deleteFile');
});