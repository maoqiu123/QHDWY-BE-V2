<?php

namespace App\Service\SJD\GKGS;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GGWXZJSYGSService
{
    //公共维修资金使用

    public function searchWxzjsyqkForYz($id,$monthNum, $wxxm, int $size)
    {
        $where = [];
        if (!empty($id)) {
            array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        }
        if (!empty($monthNum)) {
            array_push($where, ['t_xm_gs_ggwxzjsyqk.dgsrq', '>=', Carbon::now()->subMonth($monthNum)]);
        }
        if (!empty($wxxm)) {
            array_push($where, ['t_xm_gs_ggwxzjsyqk.swxxm','like','%'.$wxxm.'%']);
        }
        array_push($where, ['t_xm_gs_ggwxzjsyqk.sstatus', '=', '提交']);

        $data = DB::table('t_yz_yzjbxx')
            ->join('t_xm_gs_ggwxzjsyqk', 't_yz_yzjbxx.xmid', '=', 't_xm_gs_ggwxzjsyqk.xmid')
            ->Join('t_qyjbxx','t_qyjbxx.id','=','t_xm_gs_ggwxzjsyqk.qyid')
            ->select('t_xm_gs_ggwxzjsyqk.id', 't_xm_gs_ggwxzjsyqk.swxxm','t_qyjbxx.sqymc', 't_xm_gs_ggwxzjsyqk.dgsrq')
            ->where($where)
            ->orderBy('t_xm_gs_ggwxzjsyqk.dgsrq','desc')
            ->paginate($size);
        return $data;
    }

    public function showDetail($id){
        $data = DB::table('t_xm_gs_ggwxzjsyqk')
            ->where('id','=',$id)
            ->first();
        return $data;
    }
}
