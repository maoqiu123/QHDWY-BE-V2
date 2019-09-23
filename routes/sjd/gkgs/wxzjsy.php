<?php
//维修资金使用
Route::prefix('sjd/wxzjsy')->middleware('token')->group(function (){
    Route::get('search','SJD\GKGS\GGWXZJSYGSController@getGgwxzj');
    Route::get('detail','SJD\GKGS\GGWXZJSYGSController@getDetail');
});