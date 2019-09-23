<?php

namespace App\Service\SJD\GKGS;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WYHTLXGSService
{
    /**
     * 根据业主ID搜索一定期限内的物业合同履行情况
     * @param $id
     * @param $monthNum
     * @param string $gsmc
     * @param int $size
     * @return mixed
     */
    public function searchHtlxqkForYz($id,$monthNum,$gsmc, int $size)
    {
        $checkTime = Carbon::now()->subMonth($monthNum);
        $where = [];
        if (!empty($id)) {
            array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        }
        if (!empty($monthNum)) {
            array_push($where, ['t_xm_gs_htlxqk_zb.dgsrq', '>=', $checkTime]);
        }
        if (!empty($gsmc)){
            array_push($where,['t_xm_gs_htlxqk_zb.tgsbt','like','%'.$gsmc.'%']);
        }
//        if ($publicity_end !== null) {
//            array_push($where, ['t_xm_gs_htlxqk_zb.dgsrq', '<=', $publicity_end]);
//        }
        array_push($where, ['t_xm_gs_htlxqk_zb.sstatus', '=', '提交']);
        $data = DB::table('t_yz_yzjbxx')
            ->Join('t_xm_ba_ht', 't_yz_yzjbxx.xmid', '=', 't_xm_ba_ht.xmid')
            ->Join('t_xm_gs_htlxqk_zb', 't_xm_ba_ht.id', '=', 't_xm_gs_htlxqk_zb.htid')
            ->Join('t_qyjbxx','t_qyjbxx.id','=','t_xm_ba_ht.qyid')
            ->select('t_xm_gs_htlxqk_zb.id','t_qyjbxx.sqymc' , 't_xm_gs_htlxqk_zb.tgsbt','t_xm_gs_htlxqk_zb.dgsrq')
            ->where($where)
            ->orderBy('t_xm_gs_htlxqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }

    /**
     *  根据合同履行Id查询详情
     * @param $id
     * @return mixed
     */
    public function showDetail($id){
        $data = DB::table('t_xm_gs_htlxqk_zb')
            ->where('id','=',$id)
            ->first();
        return $data;
    }

}
