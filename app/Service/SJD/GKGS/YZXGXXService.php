<?php

namespace App\Service\SJD\GKGS;

use Illuminate\Support\Facades\DB;

class YZXGXXService
{
    //业委会信息
    public function getYwhjbxx($yzid){
        $data = DB::table('t_yz_yzjbxx')
            ->where('t_yz_yzjbxx.id',$yzid)
            ->join('t_ywh_jbxx','t_ywh_jbxx.xmid','=','t_yz_yzjbxx.xmid')
            ->select('t_ywh_jbxx.*')
            ->first();
        return $data;
    }
    //物业企业信息
    public function getQyjbxx($yzid){
        $data = DB::table('t_yz_yzjbxx')
            ->where('t_yz_yzjbxx.id',$yzid)
            ->join('t_xm_jbxx','t_yz_yzjbxx.xmid','=','t_xm_jbxx.id')
            ->join('t_qyjbxx','t_xm_jbxx.sqyid','=','t_qyjbxx.id')
            ->select('t_qyjbxx.*')
            ->first();
        return $data;
    }
    //小区信息
    public function getXqxx($yzid){
        $data = DB::table('t_yz_yzjbxx')
            ->where('t_yz_yzjbxx.id',$yzid)
            ->join('t_xm_jbxx','t_yz_yzjbxx.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_jbxx.*')
            ->first();
        return $data;
    }
}
