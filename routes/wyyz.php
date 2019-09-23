<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/25
 * Time: 下午10:48
 */

/**
 * 业主账号
 */
Route::prefix('yzzh')->group(function () {
    Route::get('code', 'WYYZ\YZZHController@sendCode');
    Route::post('', 'WYYZ\YZZHController@createAccountYz');
    Route::post('login', 'WYYZ\YZZHController@login');
    Route::post('binding', 'WYYZ\YZZHController@getTokenById')->middleware(['yzzh']);
});

Route::prefix('wyyz')->middleware('token')->group(function () {

    //5.x物业项目端
    Route::prefix('xxwh')->middleware(['token'])->group(function () {
        Route::get('search', 'WYYZ\YZXXWHController@searchOwnerInfo');
        Route::post('', 'WYYZ\YZXXWHController@createOwnerInfo');
        Route::put('init', 'WYYZ\YZXXWHController@initPassword');
        Route::delete('', 'WYYZ\YZXXWHController@deleteOwnersInfo');
        Route::put('{id}', 'WYYZ\YZXXWHController@updateOwnerInfo');
    });

    Route::prefix('tssl')->group(function () {
        // 物业项目端
        Route::get('search', 'WYYZ\YZTSSLController@searchComplainInfo');
        Route::put('handle/{recoedId}', 'WYYZ\YZTSSLController@handleComplainInfo');
        Route::put('finish/{recoedId}', 'WYYZ\YZTSSLController@finishComplainInfo');
        Route::get('infoxm/{id}', 'WYYZ\YZTSSLController@getComplainInfoForXm');
        // 业委会端
        Route::get('searchforywh', 'WYXM\YZTSXXController@searchForYwh');
        Route::get('info/{id}', 'WYXM\YZTSXXController@getComplainInfoBy');

    });

    Route::prefix('bxsl')->middleware(['token'])->group(function () {
        // 物业项目端
        Route::get('search', 'WYYZ\YZBXSLController@searchRepairInfo');
        Route::put('handle/{recoedId}', 'WYYZ\YZBXSLController@handleRepairInfo');
        Route::put('finish/{recoedId}', 'WYYZ\YZBXSLController@finishRepairInfo');
        Route::get('infoxm/{id}', 'WYYZ\YZBXSLController@getRepairInfoForXm');

        // 业委会端
        Route::get('searchforywh', 'WYXM\YZBXXXController@searchForYwh');
        Route::get('info/{id}', 'WYXM\YZBXXXController@getRepairInfoBy');
    });

    Route::prefix('jysl')->middleware(['token'])->group(function () {
        // 物业项目端
        Route::get('search', 'WYYZ\YZJYSLController@searchSuggestionInfo');
        Route::put('handle/{recoedId}', 'WYYZ\YZJYSLController@handleSuggestionInfo');
        Route::put('finish/{recoedId}', 'WYYZ\YZJYSLController@finishSuggestionInfo');
        Route::get('infoxm/{id}', 'WYYZ\YZJYSLController@getSuggestionInfoForXm');

        // 业委会端
        Route::get('searchforywh', 'WYXM\YZJYXXController@searchForYwh');
        Route::get('info/{id}', 'WYXM\YZJYXXController@getSuggestionInfoBy');
    });
    //6.x业主端
    Route::prefix('tsjb')->group(function () {
        Route::get('search', 'WYYZ\YZTSSLController@searchComplainInfoOwner');
        Route::post('', 'WYYZ\YZTSSLController@addComplainInfo');
        Route::put('submit/{id}', 'WYYZ\YZTSSLController@submitComplainInfo');
        Route::put('evaluate/{id}', 'WYYZ\YZTSSLController@evaluateComplainInfo');
        Route::get('info/{id}', 'WYYZ\YZTSSLController@getComplainInfoBy');
        Route::get('type', 'WYYZ\YZTSSLController@getComplaintType');
        Route::delete('{id}', 'WYYZ\YZTSSLController@delete');
        Route::put('{id}', 'WYYZ\YZTSSLController@update');
    });

    Route::prefix('wybx')->group(function () {
        Route::get('search', 'WYYZ\YZBXSLController@searchRepairInfoOwner');
        Route::post('', 'WYYZ\YZBXSLController@addRepairInfo');
        Route::put('submit/{id}', 'WYYZ\YZBXSLController@submitRepairInfo');
        Route::put('evaluate/{id}', 'WYYZ\YZBXSLController@evaluateRepairInfo');
        Route::get('info/{id}', 'WYYZ\YZBXSLController@getRepairInfoBy');
        Route::get('type', 'WYYZ\YZBXSLController@getRepairType');
        Route::delete('{id}', 'WYYZ\YZBXSLController@delete');
        Route::put('{id}', 'WYYZ\YZBXSLController@update');
    });

    Route::prefix('yjjy')->group(function () {
        Route::get('search', 'WYYZ\YZJYSLController@searchSuggestionInfoOwner');
        Route::post('', 'WYYZ\YZJYSLController@addSuggestionInfo');
        Route::put('submit/{id}', 'WYYZ\YZJYSLController@submitSuggestionInfo');
        Route::put('evaluate/{id}', 'WYYZ\YZJYSLController@evaluateSuggestionInfo');
        Route::get('info/{id}', 'WYYZ\YZJYSLController@getSuggestionInfoBy');
        Route::get('type', 'WYYZ\YZJYSLController@getSuggestionType');
        Route::delete('{id}', 'WYYZ\YZJYSLController@delete');
        Route::put('{id}', 'WYYZ\YZJYSLController@update');
    });

    Route::prefix('wyxx')->middleware('token')->group(function (){
        Route::get('showqyjbxx','WYYZ\YZXXWHController@showQyjbxxForYz');
        Route::get('showxmjbxx','WYYZ\YZXXWHController@showXmjbxxForYz');
    });
    Route::prefix('yzzh')->middleware('token')->group(function (){
        Route::post('register','WYYZ\YZZHController@createAccountYz');
        Route::post('login','WYYZ\YZZHController@login');
    });
    /**
     * 质量评价
     */
    Route::prefix('zlpj')->group(function (){
//        Route::post('', 'LJXQ\GZGZJHRWController@create');
//        Route::put('{id}', 'LJXQ\GZGZJHRWController@update');
//        Route::delete('{id}', 'LJXQ\GZGZJHRWController@delete');
//        Route::get('search', 'LJXQ\GZGZJHRWController@search');
//        Route::get('{id}','LJXQ\GZGZJHRWController@detail');
//        Route::put('submmit/{id}', 'LJXQ\GZGZJHRWController@submmit');
//        Route::put('callback/{id}', 'LJXQ\GZGZJHRWController@callback');
//        Route::put('finish/{id}', 'LJXQ\GZGZJHRWController@finish');
//        Route::put('select/{id}', 'LJXQ\GZGZJHRWController@select');
    });
});