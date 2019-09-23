<?php

Route::prefix('sjd/ljxq/gzfa')->middleware('token')->group(function (){
    Route::post('tp','SJD\LJXQ\FAQRController@makeSure');
    Route::get('hastp','SJD\LJXQ\FAQRController@hasYzMakeSure');
});