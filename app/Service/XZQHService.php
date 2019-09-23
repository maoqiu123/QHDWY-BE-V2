<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;

class XZQHService extends BaseService{

    protected $tbName='t_xzqh';

    /**
     * 获取省列表
     * @return mixed
     */
    public function getProvinces(){
        $provinces=DB::table($this->tbName)->where('jc','=',2)->select('mc','bm')->get();
        return $provinces;
    }

    /**
     * 根据省代码获取市列表
     * @param $province
     * @return mixed
     */
    public function getCities($province){
        $cities=DB::table($this->tbName)->where([
            ['jc','=',3],
            ['bm','like',substr($province,0,2).'%']
        ])->select('mc','bm')->get();
        return $cities;
    }

    /**
     * 根据市代码获取区县列表
     * @param $city
     * @return mixed
     */
    public function getDistrict($city){
        $district=DB::table($this->tbName)->where([
            ['jc','=',4],
            ['bm','like',substr($city,0,4).'%']
        ])->select('mc','bm')->get();
        return $district;
    }

    /**
     * 根据区县获取街道办事处列表
     * @param $district
     * @return mixed
     */
    public function getBanShiChu($district){
        $banShiChu=DB::table($this->tbName)->where([
            ['jc','=',5],
            ['bm','like',substr($district,0,6).'%']
        ])->select('mc','bm')->get();
        return $banShiChu;
    }

    /**
     * 根据街道办事处代码获取居委会列表
     * @param $banShiChu
     * @return mixed
     */
    public function getJuWeiHui($banShiChu){
        $banShiChu=DB::table($this->tbName)->where([
            ['jc','=',6],
            ['bm','like',substr($banShiChu,0,9).'%']
        ])->select('mc','bm')->get();
        return $banShiChu;
    }
}