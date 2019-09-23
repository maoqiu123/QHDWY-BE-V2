<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午4:39
 */

/**
 * 老旧小区
 */
Route::prefix('ljxq')->middleware('token')->group(function (){
    /**
     * 老旧小区基本信息增删改查
     */
    Route::prefix('jbxx')->group(function (){
        Route::post('', 'LJXQ\LJXQJBXXController@create');
        Route::put('{id}', 'LJXQ\LJXQJBXXController@update');
        Route::delete('{id}', 'LJXQ\LJXQJBXXController@delete');
        Route::get('search', 'LJXQ\LJXQJBXXController@searchLjxqjbxx');
    });
    /**
     * 改造工作计划增删改查及查询老旧小区基本信息
     */
    Route::prefix('gzgzjh')->group(function (){
        Route::post('', 'LJXQ\GZGZJHController@create');
        Route::put('{id}', 'LJXQ\GZGZJHController@update');
        Route::delete('{id}', 'LJXQ\GZGZJHController@delete');
        Route::get('search', 'LJXQ\GZGZJHController@searchGzgzjh');
        Route::get('jbxx/search', 'LJXQ\GZGZJHController@searchLjxqjbxx');

    });
    /**
     * 进展情况上报
     */
    Route::prefix('gzgzjdjzqk')->group(function (){
        Route::post('file/{id}', 'LJXQ\GZGZJDJZQKController@uploadFile');
        Route::delete('file', 'LJXQ\GZGZJDJZQKController@deleteFile');
        Route::post('', 'LJXQ\GZGZJDJZQKController@create');
        Route::put('{id}', 'LJXQ\GZGZJDJZQKController@update');
        Route::delete('{id}', 'LJXQ\GZGZJDJZQKController@delete');
        Route::get('search', 'LJXQ\GZGZJDJZQKController@searchGzgzjdjzqk');
        Route::post('{id}','LJXQ\GZGZJDJZQKController@zan');
    });
    /**
     * 进展计划任务下发
     */
    Route::prefix('gzgzjhsbrw')->group(function (){
        Route::post('', 'LJXQ\GZGZJHRWController@create');
        Route::put('{id}', 'LJXQ\GZGZJHRWController@update');
        Route::delete('{id}', 'LJXQ\GZGZJHRWController@delete');
        Route::get('search', 'LJXQ\GZGZJHRWController@search');
        Route::get('{id}','LJXQ\GZGZJHRWController@detail');
        Route::put('submmit/{id}', 'LJXQ\GZGZJHRWController@submmit');
        Route::put('callback/{id}', 'LJXQ\GZGZJHRWController@callback');
        Route::put('finish/{id}', 'LJXQ\GZGZJHRWController@finish');
        Route::put('select/{id}', 'LJXQ\GZGZJHRWController@select');
    });
    /**
     * 进展计划任务上报
     */
    Route::prefix('gzgzjhcbdwsbnr')->group(function (){
        Route::post('', 'LJXQ\GZGZJHCBDWSBNRController@create');
        Route::put('{id}', 'LJXQ\GZGZJHCBDWSBNRController@update');
        Route::get('search', 'LJXQ\GZGZJHCBDWSBNRController@search');
        Route::get('{id}','LJXQ\GZGZJHCBDWSBNRController@detail');
        Route::delete('{id}', 'LJXQ\GZGZJHCBDWSBNRController@delete');
        Route::put('submmit/{id}', 'LJXQ\GZGZJHCBDWSBNRController@submmit');
    });
    /**
     * 通知公告
     */
    Route::prefix('tzgg')->group(function (){
        Route::post('', 'LJXQ\LJXQTZGGController@create');
        Route::put('{id}', 'LJXQ\LJXQTZGGController@update');
        Route::delete('{id}', 'LJXQ\LJXQTZGGController@delete');
        Route::get('search', 'LJXQ\LJXQTZGGController@search');
        Route::get('{id}','LJXQ\LJXQTZGGController@detail');
        Route::put('submmit/{id}', 'LJXQ\LJXQTZGGController@submmit');
    });
});