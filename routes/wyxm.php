<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/16
 * Time: 下午4:32
 */
//use Illuminate\Support\Facades\Route;

/**
 * 物业项目管理
 */
Route::prefix('wyxmgl')->middleware('token')->group(function () {
    /**
     * 项目基本信息
     */
    Route::prefix('xmjbxx')->group(function () {
        Route::get('search', 'WYXM\WYXMJBXXController@search');
        Route::get('xm/{xmId}', 'WYXM\WYXMJBXXController@showWYXM');
        Route::post('init', 'WYXM\WYXMJBXXController@initXm');
        Route::get('qy/{qyId}', 'WYXM\WYXMJBXXController@getEnterpriseXm');
        Route::post('fix','WYXM\WYXMJBXXController@fixXmxx');
        Route::get('getxmbytoken','WYXM\WYXMJBXXController@getXmxxxByToken')->middleware('token');
        Route::delete('xm/{xmid}','WYXM\WYXMJBXXController@deleteXmById')->middleware('token');
        Route::get('resetp/{xmid}','WYXM\WYXMJBXXController@resetPassword')->middleware('token');
        Route::post('file/{id}','WYXM\WYXMJBXXController@uploadFile');
        Route::delete('file', 'WYXM\WYXMJBXXController@deleteFile');

        Route::post('updatebz/{id}','WYXM\WYXMJBXXController@updateSbz');
        Route::get('getbz/{id}','WYXM\WYXMJBXXController@getTbz');
    });
    /**
     * 项目交接
     */
    Route::prefix('xmjj')->group(function () {
        Route::get('qy/getbyxydm/{xydm}','WYXM\XMJJController@getQy');
        Route::get('qy/bytoken','WYXM\XMJJController@getQyByXm')->middleware('token');
        Route::get('xmjgzt','WYXM\XMJJController@isXmCanBeJieGuan')->middleware('token');
        Route::post('qyrz','WYXM\XMJJController@qyRz')->middleware('token');
        Route::post('qytc','WYXM\XMJJController@qyTc')->middleware('token');
        Route::get('qys/bytoken','WYXM\XMJJController@searchJjJl')->middleware('token');
    });


    /**
     * 已入住信息
     */
    Route::prefix('yrzxx')->group(function () {
        Route::get('search', 'WYXM\YRZXXController@search');
        Route::post('', 'WYXM\YRZXXController@createYrzxx');
        Route::get('xm/total/{xmid}','WYXM\YRZXXController@getTotalInfoByXmid');
        Route::get('xm/{xmId}', 'WYXM\YRZXXController@show');
        Route::post('fix/{id}','WYXM\YRZXXController@update')->middleware('token');
        Route::get('searchforxm','WYXM\YRZXXController@searchByXmid')->middleware('token');
        Route::get('show/{id}','WYXM\YRZXXController@searchbyid')->middleware('token');
        Route::post('create','WYXM\YRZXXController@create')->middleware('token');
        Route::delete('{id}','WYXM\YRZXXController@delete')->middleware('token');
    });
    /**
     * 备案合同
     */
    Route::prefix('baht')->group(function () {
        //房管局端
//        Route::get('search', 'WYXM\BAHTController@searchForFgj');暂时不用
        Route::get('searchforfgj', 'WYXM\BAHTController@searchHtForFgj');
        Route::get('hts/{xmid}','WYXM\BAHTController@getBahtByXmid');
        Route::get('ht/{htid}', 'WYXM\BAHTController@getBahtByHtId');

        //物业项目端口
        Route::post('file/{id}','WYXM\BAHTController@uploadFile')->middleware('token');
        Route::delete('file', 'WYXM\BAHTController@deleteFile');
        Route::get('search', 'WYXM\BAHTController@searchHtForXm')->middleware('token');
        Route::post('', 'WYXM\BAHTController@createHt')->middleware('token');
        Route::put('{id}', 'WYXM\BAHTController@updateHt')->middleware('token');
        Route::post('submit/{id}', 'WYXM\BAHTController@submitHt')->middleware('token');
        Route::get('{id}', 'WYXM\BAHTController@getHt')->middleware('token');
        Route::delete('{id}', 'WYXM\BAHTController@deleteHt')->middleware('token');

    });
    /**
     *  设备设施备案
     */
    Route::prefix('sbss')->group(function () {
        //房管局端
        Route::get('searchforfgj', 'WYXM\BASBSSController@searchSbssForFgj');
        Route::get('sbsss/{xmid}','WYXM\BASBSSController@getSbssByXmid');
        Route::get('sbss/{sbid}', 'WYXM\BASBSSController@getSbssBySbid');

        //物业项目端口
        Route::get('search', 'WYXM\BASBSSController@searchSbssForXm')->middleware('token');
        Route::post('', 'WYXM\BASBSSController@createSbss')->middleware('token');
        Route::put('{id}', 'WYXM\BASBSSController@updateSbss')->middleware('token');
        Route::post('submit/{id}', 'WYXM\BASBSSController@submitSbss')->middleware('token');
        Route::get('{id}', 'WYXM\BASBSSController@getSbss')->middleware('token');
        Route::delete('{id}', 'WYXM\BASBSSController@deleteSbss')->middleware('token');
    });
    /**
     * 承接查验
     */
    Route::prefix('cjcy')->group(function () {
        //房管局端
        Route::get('searchforfgj', 'WYXM\BACJCYController@searchCjcyForFgj');
        Route::get('cjcy/{cjcyid}', 'WYXM\BACJCYController@getCjcyByCjcyId');
        Route::get('cjcys/{xmid}','WYXM\BACJCYController@getCjcysByXmid');

        //物业项目端口
        Route::post('file/{id}','WYXM\BACJCYController@uploadFile');
        Route::delete('file', 'WYXM\BACJCYController@deleteFile');
        Route::get('search', 'WYXM\BACJCYController@searchCjcyForXm')->middleware('token');
        Route::post('', 'WYXM\BACJCYController@createCjcy')->middleware('token');
        Route::put('{id}', 'WYXM\BACJCYController@updateCjcy')->middleware('token');
        Route::post('submit/{id}', 'WYXM\BACJCYController@submitCjcy')->middleware('token');
        Route::get('get/{id}', 'WYXM\BACJCYController@getCjcy')->middleware('token');
        Route::delete('{id}', 'WYXM\BACJCYController@deleteCjcy')->middleware('token');
        Route::get('getqymc','WYXM\BACJCYController@getQymc');

    });
    /**
     * 业委会基本信息
     */
    Route::prefix('ywhjbxx')->group(function () {
        Route::get('search', 'WYXM\YWHJBXXController@search')->middleware(['token']);
//        Route::get('xmmc/{}', 'WYXM\YRZXXController@show');
        Route::get('initpassword/{ywhid}','WYXM\YWHJBXXController@initPassword');
        Route::get('getbytoken','WYXM\YWHJBXXController@getYwhxxxxForYwh')->middleware('token');
        Route::put('fix','WYXM\YWHJBXXController@fix')->middleware('token');
    });
    /**
     * 业委会工作记录及决定
     */
    Route::prefix('ywhgzjl')->group(function () {
        Route::get('search', 'WYXM\YWHGZJLController@search');
        //ywh
        Route::get('searchforywh', 'WYXM\YWHGZJLController@searchForYwh')->middleware(['token']);
        Route::post('save','WYXM\YWHGZJLController@saveGzjl')->middleware(['token']);
        Route::post('submit','WYXM\YWHGZJLController@submitGzjls')->middleware(['token']);
    });

    /**
     * 业委会成员信息
     */
    Route::prefix('ywhcy')->group(function () {
        Route::get('searchforywh', 'WYXM\YWHCYController@searchForYwh')->middleware(['token']);
        Route::get('get/{cyid}','WYXM\YWHCYController@getCyInfoById');
        Route::get('list','WYXM\YWHCYController@getCyxxList');
        Route::post('create','WYXM\YWHCYController@createCyxx');
        Route::delete('{id}','WYXM\YWHCYController@deleteCyxx');
        Route::put('update/{id}','WYXM\YWHCYController@updateCyxx');
        Route::put('change/{id}','WYXM\YWHCYController@changeStatus');
    });
    /**
     * 业主投诉信息
     */
    Route::prefix('yztsxx')->group(function () {
        Route::get('search', 'WYXM\YZTSXXController@search')->middleware(['token']);
    });
    /**
     * 业主报修信息
     */
    Route::prefix('yzbxxx')->group(function () {
        Route::get('search', 'WYXM\YZBXXXController@search')->middleware(['token']);
    });

    Route::get('xmxgxx/{xmid}','WYXM\WYXMJBXXController@getXmXgxx');

    /**
     * 项目经理
     */
    Route::prefix('xmjl')->group(function (){
        Route::get('search','WYXM\XMJLController@searchForFgj');
        Route::get('xxxx/{jlid}','WYXM\XMJLController@getXmjlXxxx');
        Route::post('create','WYXM\XMJLController@create')->middleware('token');
        Route::get('searchforxm','WYXM\XMJLController@searchForXm')->middleware('token');
        Route::post('update/{id}','WYXM\XMJLController@update')->middleware('token');
        Route::delete('{id}','WYXM\XMJLController@delete')->middleware('token');
    });
    /**
     * 项目人员配置
     */
    Route::prefix('rypz')->middleware('token')->group(function (){
        Route::post('fix','WYXM\RYPZController@update');
        Route::get('searchforxm','WYXM\RYPZController@searchForXm');
        Route::get('show/{id}','WYXM\RYPZController@searchbyid');
    });
    /**
     * 业主信息
     */
    Route::prefix('yzxx')->middleware('token')->group(function (){
       Route::post('create','WYXM\YZXXController@create');
       Route::get('searchforxm','WYXM\YZXXController@searchForXm');
    });
    /**
     * 项目收费标准
     */
    Route::prefix('sfbz')->middleware('token')->group(function (){
        Route::post('create','WYXM\SFBZController@createSfbz');
        Route::get('searchforxm','WYXM\SFBZController@searchForXm');
        Route::put('update/{id}','WYXM\SFBZController@updateSfbz');
        Route::put('submit/{id}','WYXM\SFBZController@submitSfbz');
        Route::delete('delete/{id}','WYXM\SFBZController@deleteForXm');
        Route::get('detail/{id}','WYXM\SFBZController@getSfbz');
    });
    /**
     * 通知公告
     */
    Route::prefix('tzgg')->middleware('token')->group(function (){
        Route::post('create','WYXM\TZGGController@create');
        Route::post('update/{id}','WYXM\TZGGController@update');
        Route::put('publish/{id}','WYXM\TZGGController@publish');
        Route::delete('delete/{id}','WYXM\TZGGController@delete');
        Route::post('file/{id}','WYXM\TZGGController@uploadFile');
        Route::delete('file','WYXM\TZGGController@deleteFile');
        Route::get('searchforfgj','WYXM\TZGGController@searchForFgj');
        Route::get('detail/{id}','WYXM\TZGGController@getTzgg');
    });
});