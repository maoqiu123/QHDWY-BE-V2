<?php

namespace App\Service\WYXM;

use App\Service\BaseService;
use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class BACJCYService extends BaseService{

    protected $tbName = 't_xm_ba_cjcy';

    public function getCjcyByXmid($xmid,$size){
        $cjcys=DB::table($this->tbName)
            ->where('xmid','=',$xmid)
            ->paginate($size);
        return $cjcys;
    }
    public function getCjcyByCjcyid($cjcyid){
        $cjcy = DB::table($this->tbName)
            ->join('t_xm_jbxx', 't_xm_ba_cjcy.xmid' ,'=','t_xm_jbxx.id')
            ->where('t_xm_ba_cjcy.id',$cjcyid)
            ->select('t_xm_jbxx.sxmmc', 't_xm_ba_cjcy.*')
            ->first();
        return $cjcy;
    }

    public function searchCjcyForXm($xmid,$type,$startTime,$endTime,$size)
    {
        $where = [];

        if (!empty($type)) {
            array_push($where, ['scylb', '=',$type]);
        }
        if (!empty($startTime)) {
            array_push($where, ['dcysj', '>=', $startTime]);
        }
        if (!empty($endTime)) {
            array_push($where, ['dcysj', '<=', $endTime]);
        }

        $Cjcys = DB::table($this->tbName)
            ->join('t_qyjbxx','t_qyjbxx.id','=','t_xm_ba_cjcy.sqyid')
            ->where('xmid', '=', $xmid)
            ->where($where)
            ->select('t_xm_ba_cjcy.id','t_qyjbxx.sqymc','t_xm_ba_cjcy.xmid','t_xm_ba_cjcy.sxmbm',
                't_xm_ba_cjcy.skfjsdw','t_xm_ba_cjcy.sywhmc','t_xm_ba_cjcy.swyfuqy','t_xm_ba_cjcy.scylb',
                't_xm_ba_cjcy.dcysj','t_xm_ba_cjcy.scyjg','t_xm_ba_cjcy.sstatus','t_xm_ba_cjcy.dcysj','t_xm_ba_cjcy.ffj')
            ->paginate($size);

        foreach ($Cjcys as $item){
            $item->dcysj = SqlTool::getNYRTime($item->dcysj);
        }

        return $Cjcys;
    }

    public function submitCjcy($id)
    {
        $num = DB::table($this->tbName)->where([
            ['id', '=', $id],
            ['sstatus', '=', '暂存']
        ])->update(['sstatus' => '提交']);
        return $num == 0;
    }

    public function deleteCjcy($id)
    {
        $res = DB::table($this->tbName)->where('id', $id)->first();
        if ($res != null && $res->sstatus != '提交'){
            $this->delete($id);
            return true;
        }
        return false;
    }

    public function getCjcyEditStatus($id)
    {
        $status =  DB::table($this->tbName)->where('id',$id)->select('sstatus')->first();
        if($status!=null && $status->sstatus === '提交')
            return false;
        return true;
    }

    public function searchCjcyForFgj($sxmmc,$skfjsdw,$swyfuqy,$scylb,$dstart,$dend,$size)
    {
        $where = [];

        $where[] = [
            $this->tbName.'.sstatus', '=', '提交'
        ];

        if (!empty($sxmmc)) {
            array_push($where, ['t_xm_jbxx.sxmmc', 'like', '%'.$sxmmc.'%']);
        }
        if (!empty($skfjsdw)) {
            array_push($where, ['t_xm_ba_cjcy.skfjsdw', 'like', '%'.$skfjsdw.'%']);
        }
        if (!empty($swyfuqy)) {
            array_push($where, ['t_xm_ba_cjcy.swyfuqy', 'like', '%'.$swyfuqy.'%']);
        }
        if (!empty($scylb)) {
            array_push($where, ['t_xm_ba_cjcy.scylb', '=', $scylb]);
        }
        if (!empty($dstart)) {
            array_push($where, ['t_xm_ba_cjcy.dcysj', '>=', $dstart]);
        }
        if (!empty($dend)) {
            array_push($where, ['t_xm_ba_cjcy.dcysj', '<=', $dend]);
        }
        $Cjcys = DB::table($this->tbName)
            ->join('t_xm_jbxx', 't_xm_ba_cjcy.xmid' ,'=','t_xm_jbxx.id')
            ->where($where)
            ->select('t_xm_ba_cjcy.id', 't_xm_ba_cjcy.xmid','t_xm_ba_cjcy.sxmbm','t_xm_ba_cjcy.skfjsdw','t_xm_ba_cjcy.sywhmc','t_xm_ba_cjcy.swyfuqy','t_xm_ba_cjcy.scylb','t_xm_ba_cjcy.dcysj','t_xm_ba_cjcy.scyjg','t_xm_ba_cjcy.sstatus','t_xm_jbxx.sxmmc','t_xm_ba_cjcy.ffj')
            ->paginate($size);
        foreach ($Cjcys as $data) {
            $data->ffj = json_decode($data->ffj);
            if ($data->ffj != null) {
                foreach ($data->ffj as $key => $value)
                    $data->ffj->$key = json_decode($value);
            }
        }

        return $Cjcys;
    }

    public function getQymc($id)
    {
        $qymc=DB::table('t_xm_jbxx')->where('id',$id)->select('szbqymc')->first();
        return $qymc;
    }

}