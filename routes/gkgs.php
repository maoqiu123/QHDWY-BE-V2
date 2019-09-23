<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午4:39
 */

/**
 * 公开公示
 */
Route::prefix('gkgs')->middleware('token')->group(function (){
    /**
     * 合同履行情况公示
     */
    Route::prefix('htlxqk')->group(function (){

        /** 房管局端 */
        Route::get('search', 'GKGS\HTLXQKController@searchHtlxqk');
        /** 物业项目端 */
        Route::get('wy/search', 'GKGS\HTLXQKController@searchHtlxqkForWy');
        Route::get('wy/part', 'GKGS\HTLXQKController@searchHtForWy');
        Route::post('create','GKGS\HTLXQKController@createHtlxqk');
        Route::put('save/{id}','GKGS\HTLXQKController@zanHtlxqk');
        Route::put('update/{id}','GKGS\HTLXQKController@updateHtlxqk');
        Route::get('show/{id}','GKGS\HTLXQKController@showHtlxqk');
        Route::post('file/{id}','GKGS\HTLXQKController@uploadFile');
        Route::delete('file', 'GKGS\HTLXQKController@deleteFile');
        Route::get('file/{id}','GKGS\HTLXQKController@downloadFile');
        Route::get('list/{xmid}','GKGS\HTLXQKController@getHtlxqkListByXmId');
        Route::delete('delete/{id}','GKGS\HTLXQKController@delete');
        /**
         * 业主端
         */
        Route::get('yz/search', 'GKGS\HTLXQKController@searchHtlxqkForYz');
        Route::put('yz/detail/{htlxqkId}', 'GKGS\HTLXQKController@showDetail');
        /** 业委会 */
        Route::get('ywh/search', 'GKGS\HTLXQKController@searchHtlxqkForYwh')->middleware(['token']);
    });
    /**
     * 委托经营收支情况公示
     */
    Route::prefix('wtjyszqk')->group(function (){

        /**
         * 房管局端
         */
        Route::get('search', 'GKGS\WTJYSZQKController@searchWtjyszqk');
        /**  物业项目端 */
        Route::get('wy/search', 'GKGS\WTJYSZQKController@searchWtjyszqkForWy');
        Route::post('create','GKGS\WTJYSZQKController@createWtjyszqk');
        Route::put('update/{id}','GKGS\WTJYSZQKController@updateWtjyszqk');
        Route::put('save/{id}','GKGS\WTJYSZQKController@zanWtjyszqk');
        Route::post('file/{id}','GKGS\WTJYSZQKController@uploadFile');
        Route::delete('file', 'GKGS\WTJYSZQKController@deleteFile');
        Route::get('file/{id}','GKGS\WTJYSZQKController@downloadFile');
        Route::delete('delete/{id}','GKGS\WTJYSZQKController@delete');
        /**
         * 业主端
         */
        Route::get('yz/search', 'GKGS\WTJYSZQKController@searchWtjyszqkForYz');
        Route::put('yz/detail/{wtjyszqkId}', 'GKGS\WTJYSZQKController@showDetail');
        /** 业委会 */
        Route::get('ywh/search', 'GKGS\WTJYSZQKController@searchWtjyszqkForYwh')->middleware(['token']);
    });
    /**
     * 公共分摊用水用电公示
     */
    Route::prefix('ggftysydgs')->group(function (){

        Route::get('search', 'GKGS\GGFTYSYDGSController@searchGgftysydgs');
        /** 物业项目端 */
        Route::get('wy/search', 'GKGS\GGFTYSYDGSController@searchGgftysydgsForWy');
        Route::post('create','GKGS\GGFTYSYDGSController@createGgftysydgs');
        Route::put('save/{id}','GKGS\GGFTYSYDGSController@zanGgftysydgs');
        Route::put('update/{id}','GKGS\GGFTYSYDGSController@updateGgftysydgs');
        Route::get('show/{id}','GKGS\GGFTYSYDGSController@showGgftysydgs');
        Route::post('file/{id}','GKGS\GGFTYSYDGSController@uploadFile');
        Route::delete('file', 'GKGS\GGFTYSYDGSController@deleteFile');
        Route::delete('{id}','GKGS\GGFTYSYDGSController@deleteGgftysydgs');
        Route::get('file/{id}','GKGS\GGFTYSYDGSController@downloadFile');

        /**
         * 业主端
         */
        Route::get('yz/search', 'GKGS\GGFTYSYDGSController@searchGgftysydgsForYz');
        Route::put('yz/detail/{ggftysydgsId}', 'GKGS\GGFTYSYDGSController@showDetail');
        /** 业委会 */
        Route::get('ywh/search','GKGS\GGFTYSYDGSController@searchForYwh')->middleware(['token']);
    });
    /**
     * 公共维修资金使用公示
     */
    Route::prefix('ggwxzjsyqk')->group(function (){

        Route::get('search', 'GKGS\GGWXZJSYQKController@searchGgwxzjsyqk');
        /** 物业项目端 */
        Route::get('wy/search', 'GKGS\GGWXZJSYQKController@searchWxzjsyqkForWy');
        Route::post('create','GKGS\GGWXZJSYQKController@createGgwxzjsyqk');
        Route::put('update/{id}','GKGS\GGWXZJSYQKController@updateGgwxzjsyqk');
        Route::put('save/{id}','GKGS\GGWXZJSYQKController@zanGgwxzjsyqk');
        Route::get('file/{id}','GKGS\GGWXZJSYQKController@downloadFile');
        Route::delete('delete/{id}','GKGS\GGWXZJSYQKController@delete');
        /**
         * 业主端
         */
        Route::get('yz/search', 'GKGS\GGWXZJSYQKController@searchWxzjsyqkForYz');
        Route::put('yz/detail/{ggwxzjsyqkId}', 'GKGS\GGWXZJSYQKController@showDetail');
        /** 业委会 */
        Route::get('ywh/search','GKGS\GGWXZJSYQKController@searchForYwh')->middleware(['token']);
    });

});