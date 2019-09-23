<?php

namespace App\Service\SJD\GKGS;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class YWHGZJLGSService
{
    //业委会工作记录

    public function search($id,$monthNum, $gsbt, int $size){
        $checkTime = Carbon::now()->subMonth($monthNum);
        $where = [];
        if (!empty($id)) {
            array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        }
        if (!empty($monthNum)) {
            array_push($where, ['t_ywh_gzjl.dhyrq', '>=', $checkTime]);
        }
        if (!empty($gsbt)){
            array_push($where,['t_ywh_gzjl.shybt','like','%'.$gsbt.'%']);
        }
        array_push($where, ['t_ywh_gzjl.sstatus', '=', '提交']);

        $data = DB::table('t_yz_yzjbxx')
            ->join('t_ywh_jbxx','t_ywh_jbxx.xmid','=','t_yz_yzjbxx.xmid')
            ->join('t_ywh_gzjl','t_ywh_gzjl.ywhid','=','t_ywh_jbxx.id')
            ->where($where)
            ->select('t_ywh_gzjl.id','t_ywh_gzjl.shybt','t_ywh_gzjl.dhyrq')
            ->paginate($size);
        return $data;

    }

    public function showDetail($id){
        $data = DB::table('t_ywh_gzjl')
            ->where('id',$id)
            ->first();
        return $data;
    }

}
