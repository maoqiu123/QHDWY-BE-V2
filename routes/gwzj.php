<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/25
 * Time: 下午1:11
 */
Route::prefix('gwzj')->middleware('token')->group(function () {

    Route::prefix('spxm')->group(function () {
        Route::get('search', 'GWZJ\SPXMController@searchProject');
        Route::get('{id}', 'GWZJ\SPXMController@showProject');
        Route::get('record/{projectId}', 'GWZJ\XMSPGCController@getApproveRecord');
    });
});