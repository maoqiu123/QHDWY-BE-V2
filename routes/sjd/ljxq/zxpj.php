<?php

Route::prefix('sjd/ljxq/zxpj')->middleware('token')->group(function (){
    Route::post('dafen','SJD\LJXQ\ZXPJController@dafen');
    Route::get('hasdafen','SJD\LJXQ\ZXPJController@hasDafen');
});