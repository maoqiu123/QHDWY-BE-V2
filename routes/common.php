<?php
// 行政区划级练选择
Route::get('/xzqh/provinces','WYQY\CommonController@getProvinces');
Route::get('/xzqh/cities/{provinceId}','WYQY\CommonController@getCities');
Route::get('/xzqh/district/{cityId}','WYQY\CommonController@getDistrict');
Route::get('/xzqh/jiedao/{districtId}','WYQY\CommonController@getBanShiChu');
Route::get('/xzqh/juweihui/{banshichu}','WYQY\CommonController@getJuWeiHui');
// 企业编号是否存在
Route::get('/exist/qiye/{enterpriseId}','WYQY\CommonController@checkEnterpriseExist');
// 企业名称提示
Route::get('/remind/{hint}','WYQY\CommonController@hintEnterpriseName');
// 下载文件
Route::get('/file/{id}', 'WYQY\CommonController@downloadFile');
//删除文件
Route::delete('/file','WYQY\CommonController@deleteFile');

Route::get('/code','WYQY\CommonController@codeList');
