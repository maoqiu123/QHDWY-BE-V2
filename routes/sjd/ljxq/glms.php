<?php

Route::prefix('sjd/ljxq/glms')->middleware('token')->group(function (){
    Route::post('qr','SJD\LJXQ\GLMSController@makeSure');
    Route::get('hasqr','SJD\LJXQ\GLMSController@hasQr');
});