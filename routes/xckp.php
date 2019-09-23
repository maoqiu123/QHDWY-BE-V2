<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/18
 * Time: 下午8:55
 */
Route::prefix('xckp')->middleware('token')->group(function () {

    Route::prefix('jcrw')->group(function () {
        Route::get('search', 'XCKP\JCRWController@searchInspectionTask');
        Route::post('', 'XCKP\JCRWController@createInspectionTask');
        Route::put('{id}', 'XCKP\JCRWController@updateInspectionTask');
        Route::delete('', 'XCKP\JCRWController@deleteInspectionTask');
        Route::get('', 'XCKP\JCRWController@getInspectionTask');

    });

    Route::prefix('xmfp')->group(function () {
        Route::get('search/{taskId}', 'XCKP\JCRWController@searchAllocalTask');
        Route::get('selected/{taskId}', 'XCKP\JCRWController@getAllocaledTask');
        Route::put('{taskId}', 'XCKP\JCRWController@saveAllocalStatus');
    });

    Route::prefix('jcdx')->group(function () {
        Route::get('search', 'XCKP\JCDXController@searchInspectionMajorTerm');
        Route::post('{taskId}', 'XCKP\JCDXController@createInspectionMajorTerm');
        Route::put('{id}', 'XCKP\JCDXController@updateInspectionMajorTerm');
        Route::delete('', 'XCKP\JCDXController@deleteInspectionMajorTerm');
    });

    Route::prefix('jcbz')->group(function () {
        Route::get('search', 'XCKP\JCBZController@searchInspectionStandard');
        Route::post('', 'XCKP\JCBZController@createInspectionStandard');
        Route::put('{id}', 'XCKP\JCBZController@updateInspectionStandard');
        Route::delete('', 'XCKP\JCBZController@deleteInspectionStandard');
        Route::get('jcdxby/{taskId}', 'XCKP\JCBZController@getInspectionMajorTermByTaskId');
    });

    Route::prefix('qyzj')->group(function () {
        Route::get('search', 'XCKP\QYZJController@searchSelfRecord');
        Route::post('', 'XCKP\QYZJController@saveSelfCheck');
        Route::put('', 'XCKP\QYZJController@submitSelfRecord');
        Route::get('jcrw', 'XCKP\QYZJController@getInspectionTask');
        Route::get('jcxm/{taskId}', 'XCKP\QYZJController@getInspectionProject');
    });

    Route::prefix('jcjl')->group(function () {
        Route::get('search', 'XCKP\JCJLController@searchInspectionResult');
        Route::post('', 'XCKP\JCJLController@saveInspectionResult');
        Route::put('', 'XCKP\JCJLController@submitInspectionResult');
        Route::get('jcrw', 'XCKP\JCJLController@getInspectionTask');
        Route::get('jcxm/{taskId}', 'XCKP\JCJLController@getInspectionProject');
    });

    Route::prefix('zgtz')->group(function () {
        Route::get('search', 'XCKP\JCJLController@searchUnqualifiedResult');
        Route::get('jcxm/{taskId}', 'XCKP\JCJLController@getUnqualifiedProject');
        Route::post('print', 'XCKP\JCJLController@printNotice');
    });

    Route::prefix('jglr')->group(function () {
        Route::get('jcxm/{taskId}', 'XCKP\JCJLController@getUnqualifiedUnSubmitProject');
        Route::get('search', 'XCKP\JCJLController@searchResultEntry');
        Route::post('', 'XCKP\JCJLController@saveResultEntry');
        Route::put('', 'XCKP\JCJLController@submitResultEntry');
    });

});



