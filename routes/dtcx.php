<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/16
 * Time: 下午4:32
 */


/**
 * 地图查询
 */
Route::prefix('dtcx')->group(function () {

    /**
     * 企业信息查询
     */
    Route::prefix('qyxx')->group(function () {
        Route::get('name/search', 'DTCX\DTCXController@searchEnterpriseByName');
        Route::get('region/search', 'DTCX\DTCXController@searchEnterpriseByRegion');
        Route::get('jbxx/{qyid}', 'DTCX\DTCXController@getQyjbxx');
        Route::get('xms/{qyid}', 'DTCX\DTCXController@getQyxms');
        Route::get('gkgs/{qyid}', 'DTCX\DTCXController@getQygkgs');
        Route::get('tsbx/{qyid}', 'DTCX\DTCXController@getQytsbx');
        Route::get('htlxqk/{qyid}','DTCX\DTCXController@getHtlxqkByQyId');
        Route::get('wtjysz/{qyid}','DTCX\DTCXController@getWtjyszqkByQyId');
        Route::get('ggsdfyft/{qyid}','DTCX\DTCXController@getGgsdfyftByQyId');
        Route::get('wxjjsy/{qyid}','DTCX\DTCXController@getWxjjsyqkByQyId');
    });
    /**
     * 物业项目查询
     */
    Route::prefix('wyxm')->group(function () {
        Route::get('name/search', 'DTCX\DTCXController@searchXmByName');
        Route::get('xzqh/{xzqh}', 'DTCX\DTCXController@getLjxqByXzqh');
        Route::get('jbxx/{xmid}', 'DTCX\DTCXController@getXmjbxx');
        Route::get('qyxx/{xmid}', 'DTCX\DTCXController@getXmqyxx');
        Route::get('gkgs/{xmid}', 'DTCX\DTCXController@getXmgkgs');
        Route::get('tsbx/{xmid}', 'DTCX\DTCXController@getXmtsbx');
        Route::get('htlxqk/{xmid}','DTCX\DTCXController@getHtlxqkByXmId');
        Route::get('wtjysz/{xmid}','DTCX\DTCXController@getWtjyszqkByXmId');
        Route::get('ggsdfyft/{xmid}','DTCX\DTCXController@getGgsdfyftByXmId');
        Route::get('wxjjsy/{xmid}','DTCX\DTCXController@getWxjjsyqkByXmId');
    });
    /**
     * 老旧小区查询
     */
    Route::prefix('ljxq')->group(function () {
        Route::get('xzqh/{xzqh}', 'DTCX\DTCXController@getLjxqByXzqh');
        Route::get('name/search', 'DTCX\DTCXController@getLjxqByName');
        Route::get('jbxx/{ljxqid}', 'DTCX\DTCXController@getLjxqById');
        Route::get('gzgzjh/{ljxqid}', 'DTCX\DTCXController@getGzgzjhByLjxqid');
        Route::get('gzgzjd/{gzgzjhid}', 'DTCX\DTCXController@getGzgzjdById');
    });

});