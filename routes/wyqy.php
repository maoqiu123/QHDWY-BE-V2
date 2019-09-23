<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/16
 * Time: 下午4:32
 */


/**
 * 物业企业管理
 */
Route::prefix('wyqygl')->middleware('token')->group(function () {

    /**
     * 企业基本信息维护
     */
    Route::prefix('jbxxwh')->group(function () {
        Route::post('file/{id}','WYQY\QYJBXXWHController@uploadFile');
        Route::delete('file', 'WYQY\QYJBXXWHController@deleteFile');

        Route::get('search', 'WYQY\QYJBXXWHController@searchEnterprise');
        Route::post('init', 'WYQY\QYJBXXWHController@initEnterprise');
        Route::delete('', 'WYQY\QYJBXXWHController@deleteEnterprise');
        Route::put('status', 'WYQY\QYJBXXWHController@updateEnterpriseStatus');
        Route::get('show/{id}', 'WYQY\QYJBXXWHController@showEnterprise');
        Route::put('code', 'WYQY\QYJBXXWHController@initEnterpriseCode');
        Route::put('{id}', 'WYQY\QYJBXXWHController@updateEnterpriseInfo');

        Route::post('create','WYQY\QYJBXXWHController@createEnterpriseByQy');

     //   Route::post('update/{id}','WYQY\QYKBXXWHController@updateEnterpriseByQy');

        Route::put('update/{id}','WYQY\QYJBXXWHController@updateEnterpriseByQy');
        Route::get('qyshow/{id}','WYQY\QYJBXXWHController@showEnterpriseByQy');

        /** 业委会 */
        Route::get('searchforywh','WYQY\QYJBXXWHController@searchQyjbxxForYwh');
        Route::get('searchxmxxforywh','WYQY\QYJBXXWHController@searchXmjbxxForYwh');

    });

    /**
     * 管理人员
     */
    Route::prefix('glry')->group(function () {
        Route::get('search', 'WYQY\QYGLRYController@searchQyglry');
//    Route::post('init', 'WYQY\QYJBXXWHController@initEnterprise');
        Route::delete('{id}', 'WYQY\QYGLRYController@deleteGlry');
//    Route::put('status/{id}', 'WYQY\QYJBXXWHController@updateEnterpriseStatus');
        Route::get('show/{id}', 'WYQY\QYGLRYController@showGlry');
//    Route::put('code/{id}', 'WYQY\QYJBXXWHController@initEnterpriseCode');

        Route::post('create','WYQY\QYGLRYController@createQyglryByQy');
        Route::put('update/{id}','WYQY\QYGLRYController@updateQyglryByQy');
        Route::get('qyshow/{id}','WYQY\QYGLRYController@showQyglryByQy');
        Route::get('glry','WYQY\QYGLRYController@getGlryByQy');

    });

    /**
     * 资质信息
     */
    Route::prefix('zzxx')->group(function () {

        Route::post('file/{id}', 'WYQY\QYZZXXController@uploadFile');
        Route::delete('file', 'WYQY\QYZZXXController@deleteFile');
        Route::get('search', 'WYQY\QYZZXXController@searchQyzzxx');

        Route::post('create','WYQY\QYZZXXController@createQyzzxxByQy');
        Route::put('update/{id}','WYQY\QYZZXXController@updateQyzzxxByQy');
        Route::get('qyshow/{id}','WYQY\QYZZXXController@showQyzzxx');
        Route::delete('{id}','WYQY\QYZZXXController@deleteQyzzxx');
        Route::get('searchforqy','WYQY\QYZZXXController@getForQy');
    });

    /**
     * 奖惩信息
     */
    Route::prefix('jcjl')->group(function () {
        Route::post('file/{id}', 'WYQY\QYJCJLController@uploadFile');
        Route::delete('file', 'WYQY\QYJCJLController@deleteFile');
        Route::get('search', 'WYQY\QYJCJLController@searchQyjcjl');

        Route::post('create','WYQY\QYJCJLController@createQyjcjlByQy');
        Route::put('update/{id}','WYQY\QYJCJLController@updateQyjcjlByQy');
        Route::get('qyshow/{id}','WYQY\QYJCJLController@showQyjcjl');
        Route::delete('{id}','WYQY\QYJCJLController@deleteQyjcjl');
        Route::get('searchforqy','WYQY\QYJCJLController@getForQy');
    });

    /**
     * 纳税信息
     */
    Route::prefix('nsxx')->group(function () {
        Route::get('search', 'WYQY\QYNSXXController@searchQynsxx');
        Route::post('create','WYQY\QYNSXXController@createQynsxxByQy');
        Route::put('update/{id}','WYQY\QYNSXXController@updateQynsxxByQy');
        Route::get('qyshow/{id}','WYQY\QYNSXXController@showQynsxx');
        Route::delete('{id}','WYQY\QYNSXXController@deleteQynsxx');
        Route::get('searchforqy','WYQY\QYNSXXController@getForQy');
    });


    /**
     * 外埠信息
     */
    Route::prefix('wbxm')->group(function () {

        Route::get('search', 'WYQY\QYWBXMController@searchWbxm');
//    Route::post('init', 'WYQY\QYJBXXWHController@initEnterprise');
//    Route::delete('{id}', 'WYQY\QYGLRYController@deleteGlry');
//    Route::put('status/{id}', 'WYQY\QYJBXXWHController@updateEnterpriseStatus');
        Route::get('show/{id}', 'WYQY\QYWBXMController@showWbxm');
//    Route::put('code/{id}', 'WYQY\QYJBXXWHController@initEnterpriseCode');
        Route::post('create','WYQY\QYWBXMController@createQyWbxmByQy');
        Route::put('update/{id}','WYQY\QYWBXMController@updateQywbxmByQy');
        Route::get('qyshow/{id}','WYQY\QYWBXMController@showQywbxm');
        Route::delete('{id}','WYQY\QYWBXMController@deleteQywbxm');
        Route::get('searchforqy','WYQY\QYWBXMController@getForQy');
        Route::post('file/{id}','WYQY\QYWBXMController@uploadFile');
        Route::delete('file', 'WYQY\QYWBXMController@deleteFile');
    });

    /**
     * 外包公司信息
     */
    Route::prefix('wbgs')->group(function () {

        Route::get('search', 'WYQY\QYWBGSController@searchWbgs');
        Route::get('show/{id}', 'WYQY\QYWBGSController@showWbgs');
        Route::post('create','WYQY\QYWBGSController@createQywbgsByQy');
        Route::put('update/{id}','WYQY\QYWBGSController@updateQywbgsByQy');
        Route::get('qyshow/{id}','WYQY\QYWBGSController@showWbgs');
        Route::delete('{id}','WYQY\QYWBGSController@deleteQywbgs');
        Route::get('searchforqy','WYQY\QYWBGSController@getForQy');
        Route::post('file/{id}','WYQY\QYWBGSController@uploadFile');
    });
});