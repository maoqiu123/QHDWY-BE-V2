<?php

Route::prefix('sjd/ljxq/zxys')->middleware('token')->group(function (){
    Route::post('qr','SJD\LJXQ\ZXYSController@makeSure');
    Route::get('hasqr','SJD\LJXQ\ZXYSController@hasYzYS');
});