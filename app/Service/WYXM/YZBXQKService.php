<?php

namespace App\Service\WYXM;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class YZBXQKService extends BaseService
{
    protected $tbName = 't_yz_yzbxxx';

    public function search($xmmc, $startdate, $enddate, $qymc, $size,$bxsx,$slzt, $select = ['*'])
    {
        $where = [];
        if (!empty($xmmc)) {
            array_push($where, ['t_xm_jbxx.sxmmc', 'like', '%' . $xmmc . '%']);
        }
        if (!empty($startdate)) {
            array_push($where, ['t_yz_yzbxxx.dbxrq', '>', $startdate]);
        }
        if (!empty($enddate)) {
            array_push($where, ['t_yz_yzbxxx.dbxrq', '<', $enddate]);
        }
        if (!empty($qymc)) {
            array_push($where, ['t_qyjbxx.sqymc', 'like', '%' . $qymc . '%']);
        }
        if (!empty($bxsx)) {
            array_push($where, ['t_yz_yzbxxx.sbxsx', '=', $bxsx]);
        }
        if (!empty($slzt)) {
            array_push($where, ['t_yz_yzbxxx.sslzt', '=', $slzt]);
        }
        $data = DB::table($this->tbName)
            ->join('t_yz_yzjbxx', 't_yz_yzbxxx.yzid', '=', 't_yz_yzjbxx.id')
            ->join('t_qyjbxx', 't_qyjbxx.id', '=', 't_yz_yzbxxx.qyid')
            ->join('t_xm_jbxx', 't_xm_jbxx.id', '=', 't_yz_yzjbxx.xmid')
            ->where($where)
            ->select($select)
            ->paginate($size);
        return $data;
    }

    public function getQyBxxxNumBySlzt($qyid)
    {
        $tsxx = DB::table($this->tbName)
            ->where('qyid', $qyid)
            ->get();
        $wsl = 0;
        $wbj = 0;
        $bmy = 0;
        foreach ($tsxx as $item) {
            $slzt = $item->sslzt;
            switch ($slzt) {
                case '未处理' :
                case '未受理' :
                    $wsl++;
                    break;
                case '已受理' :
                case '未办结' :
                    $wbj++;
                    break;
            }
            $yzpj = $item->syzpj;
            if ($yzpj == '不满意')
                $bmy++;
        }
        return [
            'wsl' => $wsl,
            'wbj' => $wbj,
            'bmy' => $bmy
        ];
    }

    public function getXmtsxxNumSplitBySlzt($xmid)
    {
        $tsxx = DB::table('t_yz_yzjbxx')
            ->where('t_yz_yzjbxx.xmid', '=', $xmid)
            ->join($this->tbName, $this->tbName . '.' . 'yzid', '=', 't_yz_yzjbxx.id')
            ->select('t_yz_yzbxxx.*')
            ->get();
        $wsl = 0;
        $wbj = 0;
        $bmy = 0;
        foreach ($tsxx as $item) {
            $slzt = $item->sslzt;
            switch ($slzt) {
                case '未处理' :
                case '未受理' :
                    $wsl++;
                    break;
                case '已受理' :
                case '未办结' :
                    $wbj++;
                    break;
            }
            $yzpj = $item->syzpj;
            if ($yzpj == '不满意')
                $bmy++;
        }
        return [
            'wsl' => $wsl,
            'wbj' => $wbj,
            'bmy' => $bmy
        ];
    }

    public function searchForYwh($ywhId,$startdate,$enddate,$status,$assess,$size){
        $where=[];
        if (!empty($startdate)){
            array_push($where,['t_yz_yzbxxx.dbxrq','>',$startdate]);
        }
        if (!empty($enddate)){
            array_push($where,['t_yz_yzbxxx.dbxrq','<',$enddate]);
        }
        if (!empty($status)){
            array_push($where,['t_yz_yzbxxx.sslzt','=',$status]);
        }
        if (!empty($assess)) {
            array_push($where, ['t_yz_yzbxxx.syzpj', '=', $assess]);
        }
        $data = DB::table($this->tbName)
            ->join('t_yz_yzjbxx','t_yz_yzbxxx.yzid','=','t_yz_yzjbxx.id')
            ->join('t_qyjbxx','t_qyjbxx.id','=','t_yz_yzbxxx.qyid')
            ->join('t_xm_jbxx','t_xm_jbxx.id','=','t_yz_yzjbxx.xmid')
            ->join('t_ywh_jbxx','t_ywh_jbxx.xmid','=','t_xm_jbxx.id')
            ->where('t_ywh_jbxx.id','=',$ywhId)
            ->where($where)
            ->select('t_yz_yzbxxx.id', 't_yz_yzbxxx.dbxrq','t_qyjbxx.sqymc', 't_yz_yzbxxx.sbxsx', 't_yz_yzbxxx.sbxnr', 't_yz_yzbxxx.sslzt', 't_yz_yzbxxx.dslrq', 't_yz_yzbxxx.sblqk', 't_yz_yzbxxx.dbjrq', 't_yz_yzbxxx.syzpj', 't_yz_yzbxxx.sbmynr')
            ->orderBy('t_yz_yzbxxx.dbxrq','desc')
            ->paginate($size);
        return $data;
    }

    public function getRepairInfoBy($id)
    {
        $ownerInfo =  DB::table($this->tbName)
            ->where('t_yz_yzbxxx.id',$id)
            ->join('t_qyjbxx','t_qyjbxx.id','=','t_yz_yzbxxx.qyid')
            ->select('dbxrq','slxdh','t_qyjbxx.sqymc','sbxsx','sbxnr','sslzt','dslrq','sblqk','dbjrq','dhfrq','syzpj','dpjrq','sbmynr')
            ->first();
        return $ownerInfo;
    }
}