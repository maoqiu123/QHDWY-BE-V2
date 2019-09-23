<?php

namespace App\Service\SJD\GKGS;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GGFYFTGSService
{
    //公共费用分摊

    public function searchGgftysydgsForYz($id,int $monthNum,$gsbt, int $size)
    {
        $where = [];
        if (!empty($id)) {
            array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        }
        if (!empty($monthNum)) {
            array_push($where, ['t_xm_gs_ggftysydgs_zb.dgsrq', '>=', Carbon::now()->subMonth($monthNum)]);
        }
        if (!empty($gsbt)) {
            array_push($where, ['t_xm_gs_ggftysydgs_zb.sgsbt','like','%'.$gsbt.'%']);
        }
        array_push($where, ['t_xm_gs_ggftysydgs_zb.sstatus', '=', '提交']);

        $data = DB::table('t_yz_yzjbxx')
            ->join('t_xm_gs_ggftysydgs_zb', 't_yz_yzjbxx.xmid', '=', 't_xm_gs_ggftysydgs_zb.xmid')
            ->join('t_qyjbxx','t_qyjbxx.id','=','t_xm_gs_ggftysydgs_zb.qyid')
            ->where($where)
            ->select('t_xm_gs_ggftysydgs_zb.id', 't_xm_gs_ggftysydgs_zb.sgsbt', 't_xm_gs_ggftysydgs_zb.dgsrq','t_qyjbxx.sqymc')
            ->orderBy('t_xm_gs_ggftysydgs_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }

    public function showDetail($id){
        $data = DB::table('t_xm_gs_ggftysydgs_zb')
            ->where('id','=',$id)
            ->first();
        return $data;
    }

}
