<?php
//通知公告
Route::prefix('sjd/dbsx')->middleware('token')->group(function (){
    //投票表决
    Route::prefix('tpbj')->group(function () {
        Route::get('table/{tpid}','SJD\BMFW\DBSXController@getTpbjTable');
        Route::get('res/{tpid}','SJD\BMFW\DBSXController@getTpbjRes');
        Route::post('vote/{tpid}','SJD\BMFW\DBSXController@voteTpbj');
    });
    //质量评价
    Route::prefix('zlpj')->group(function () {
        Route::get('table/{pjid}','SJD\BMFW\DBSXController@getZlpjTable');
        Route::get('content','SJD\BMFW\DBSXController@getZlpjContent');
        Route::get('res/{pjid}','SJD\BMFW\DBSXController@getZlpjRes');
        Route::post('vote/{pjid}','SJD\BMFW\DBSXController@voteZlpj');
    });
    //待办事项
    Route::get('list','SJD\BMFW\DBSXController@getDbsxList');

});