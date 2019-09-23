<?php
// 常用电话
Route::prefix('sjd/cydh')->middleware('token')->group(function (){
    Route::get('all','SJD\BMFW\CYDHController@getCydhList');
});