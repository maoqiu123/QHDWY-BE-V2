<?php

namespace App\Service\WYXM;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class YZTSXXService extends BaseService
{
    protected $tbName = 't_yz_yztsxx';

    public function search($xmmc, $startdate, $enddate, $qymc, $size,$tssx,$slzt,$select = ['*'])
    {
        $where = [];
        if (!empty($xmmc)) {
            array_push($where, ['t_xm_jbxx.sxmmc', 'like', '%' . $xmmc . '%']);
        }
        if (!empty($startdate)) {
            array_push($where, ['t_yz_yztsxx.dtsrq', '>', $startdate]);
        }
        if (!empty($enddate)) {
            array_push($where, ['t_yz_yztsxx.dtsrq', '<', $enddate]);
        }
        if (!empty($qymc)) {
            array_push($where, ['t_qyjbxx.sqymc', 'like', '%' . $qymc . '%']);
        }
        if (!empty($tssx)) {
            array_push($where, ['t_yz_yztsxx.stssx', '=', $tssx]);
        }
        if (!empty($slzt)) {
            array_push($where, ['t_yz_yztsxx.sslzt', '=', $slzt]);
        }
        $data = DB::table($this->tbName)
            ->join('t_yz_yzjbxx', 't_yz_yztsxx.yzid', '=', 't_yz_yzjbxx.id')
            ->join('t_qyjbxx', 't_qyjbxx.id', '=', 't_yz_yztsxx.qyid')
            ->join('t_xm_jbxx', 't_xm_jbxx.id', '=', 't_yz_yzjbxx.xmid')
            ->where($where)
            ->select($select)
            ->orderBy('t_yz_yztsxx.dtsrq','desc')
            ->paginate($size);
        return $data;
    }

    public function getQyTsxxNumSplitBySlzt($qyid)
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
            ->select('t_yz_yztsxx.*')
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

    public function searchForYwh($ywhId, $startdate, $enddate, $status, $assess, $size)
    {
        $where = [];
        if (!empty($startdate)) {
            array_push($where, ['t_yz_yztsxx.dtsrq', '>', $startdate]);
        }
        if (!empty($enddate)) {
            array_push($where, ['t_yz_yztsxx.dtsrq', '<', $enddate]);
        }
        if (!empty($status)) {
            array_push($where, ['t_yz_yztsxx.sslzt', '=', $status]);
        }
        if (!empty($assess)) {
            array_push($where, ['t_yz_yztsxx.syzpj', '=', $assess]);
        }
        $data = DB::table($this->tbName)
            ->join('t_yz_yzjbxx', 't_yz_yztsxx.yzid', '=', 't_yz_yzjbxx.id')
            ->join('t_qyjbxx', 't_qyjbxx.id', '=', 't_yz_yztsxx.qyid')
            ->join('t_xm_jbxx', 't_xm_jbxx.id', '=', 't_yz_yzjbxx.xmid')
            ->join('t_ywh_jbxx', 't_ywh_jbxx.xmid', '=', 't_xm_jbxx.id')
            ->where('t_ywh_jbxx.id', '=', $ywhId)
            ->where($where)
            ->select('t_yz_yztsxx.id', 't_yz_yztsxx.dtsrq', 't_qyjbxx.sqymc', 't_yz_yztsxx.stssx', 't_yz_yztsxx.stsnr', 't_yz_yztsxx.sslzt', 't_yz_yztsxx.dslrq', 't_yz_yztsxx.sblqk', 't_yz_yztsxx.dbjrq', 't_yz_yztsxx.syzpj', 't_yz_yztsxx.sbmynr')
            ->orderBy('t_yz_yztsxx.dtsrq', 'desc')
            ->paginate($size);
        return $data;
    }

    public function getComplainInfoBy($id)
    {
        $ownerInfo = DB::table($this->tbName)
            ->where('t_yz_yztsxx.id', $id)
            ->join('t_qyjbxx', 't_qyjbxx.id', '=', 't_yz_yztsxx.qyid')
            ->select('dtsrq', 'slxdh', 't_qyjbxx.sqymc', 'stssx', 'stsnr', 'sslzt', 'dslrq', 'sblqk', 'dbjrq', 'dhfrq', 'syzpj', 'dpjrq', 'sbmynr')
            ->first();
        return $ownerInfo;
    }

}