<?php
//通知公告
Route::prefix('sjd/tzgg')->middleware('token')->group(function (){
    Route::get('search','SJD\BMFW\TZGGController@searchTzgg');
    Route::get('one/{id}','SJD\BMFW\TZGGController@getTzggInfo');
});