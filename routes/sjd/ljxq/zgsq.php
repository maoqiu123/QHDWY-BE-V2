<?php

Route::prefix('sjd/ljxq/zgsq')->middleware('token')->group(function (){
    Route::post('tp','SJD\LJXQ\ZGSQController@vote');
    Route::get('hastp','SJD\LJXQ\ZGSQController@hasYzVoted');
});