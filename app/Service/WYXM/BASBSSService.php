<?php

namespace App\Service\WYXM;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class BASBSSService extends BaseService{

    protected $tbName = 't_xm_ba_sbss';

    public function getSbssByXmid($xmid,$size){
        $sbsss=DB::table($this->tbName)
            ->where('xmid','=',$xmid)
            ->paginate($size);
        return $sbsss;
    }

    public function getSbssBySbid($sbid){
        $sbss = DB::table($this->tbName)
            ->join('t_xm_jbxx', 't_xm_ba_sbss.xmid' ,'=','t_xm_jbxx.id')
            ->where('t_xm_ba_sbss.id','=',$sbid)
            ->select('t_xm_jbxx.sxmmc', 't_xm_ba_sbss.*')
            ->first();
        return $sbss;
    }


    public function searchSbssForXm($xmid,$sbbh,$sbmc, $pp,$sccj,$gys,$size)
    {
        $where = [];

        if (!empty($sbbh)) {
            array_push($where, ['ssbbh', 'like', '%' . $sbbh . '%']);
        }
        if (!empty($sbmc)) {
            array_push($where, ['ssbmc', 'like', '%' . $sbmc . '%']);
        }
        if (!empty($pp)) {
            array_push($where, ['spp', 'like', '%' . $pp . '%']);
        }
        if (!empty($sccj)) {
            array_push($where, ['ssccj', 'like', '%' . $sccj . '%']);
        }
        if (!empty($gys)) {
            array_push($where, ['sgys', 'like', '%' . $gys . '%']);
        }

        $Sbsss = DB::table($this->tbName)
            ->where('xmid', '=', $xmid)
            ->where($where)
            ->select('id', 'xmid','sxmbm','ssbbh','ssbmc','isl','sjldw','spp','ssccj','sgys','ssyzk','sstatus')
            ->paginate($size);
        return $Sbsss;
    }

    public function submitSbss($id)
    {
        $num = DB::table($this->tbName)->where([
            ['id', '=', $id],
            ['sstatus', '=', '暂存']
        ])->update(['sstatus' => '提交']);
        return $num == 0;
    }

    public function deleteSbss($id)
    {
        $res = DB::table($this->tbName)->where('id', $id)->first();
        if ($res != null && $res->sstatus != '提交'){
            $this->delete($id);
            return true;
        }
        return false;
    }

    public function getSbssEditStatus($id)
    {
        $status =  DB::table($this->tbName)->where('id',$id)->select('sstatus')->first();
        if($status!=null && $status->sstatus === '提交')
            return false;
        return true;
    }
    public function searchSbssForFgj($sxmmc,$sbbh,$sbmc, $pp,$sccj,$gys,$ssyzk,$size)
    {
        $where = [];

        $where[] = [
            $this->tbName.'.sstatus', '=', '提交'
        ];

        if (!empty($sxmmc)) {
            array_push($where, ['t_xm_jbxx.sxmmc', 'like', '%' . $sxmmc . '%']);
        }
        if (!empty($sbbh)) {
            array_push($where, ['t_xm_ba_sbss.ssbbh', 'like', '%' . $sbbh . '%']);
        }
        if (!empty($sbmc)) {
            array_push($where, ['t_xm_ba_sbss.ssbmc', 'like', '%' . $sbmc . '%']);
        }
        if (!empty($pp)) {
            array_push($where, ['t_xm_ba_sbss.spp', 'like', '%' . $pp . '%']);
        }
        if (!empty($sccj)) {
            array_push($where, ['t_xm_ba_sbss.ssccj', 'like', '%' . $sccj . '%']);
        }
        if (!empty($gys)) {
            array_push($where, ['t_xm_ba_sbss.sgys', 'like', '%' . $gys . '%']);
        }
        if (!empty($ssyzk)) {
            array_push($where, ['t_xm_ba_sbss.ssyzk', 'like', '%' . $ssyzk . '%']);
        }

        $Sbsss = DB::table($this->tbName)
            ->join('t_xm_jbxx', 't_xm_ba_sbss.xmid' ,'=','t_xm_jbxx.id')
            ->where($where)
            ->select('t_xm_ba_sbss.id', 't_xm_ba_sbss.xmid','t_xm_jbxx.sxmmc','t_xm_ba_sbss.ssbbh','t_xm_ba_sbss.ssbmc','t_xm_ba_sbss.isl','t_xm_ba_sbss.sjldw','t_xm_ba_sbss.spp','t_xm_ba_sbss.ssccj','t_xm_ba_sbss.sgys','t_xm_ba_sbss.ssyzk','t_xm_ba_sbss.sstatus')
            ->paginate($size);
        return $Sbsss;
    }
}