<?php
//公共费用分摊
Route::prefix('sjd/ggfyft')->middleware('token')->group(function (){
    Route::get('search','SJD\GKGS\GGFYFTGSController@getGgfyft');
    Route::get('detail','SJD\GKGS\GGFYFTGSController@getDetail');
});