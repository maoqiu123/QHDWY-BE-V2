<?php

namespace App\Service\WYXM;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class BAHTService extends BaseService
{

    protected $tbName = 't_xm_ba_ht';

    public function search($xmbm, $htmc, $htlx, $qymc, $fzr, $fzrlxdh, $startdate, $enddate, $size, $select = ['*'])
    {
        $where = [];

        if (!empty($xmbm)) {
            array_push($where, ['sxmbm', '=', $xmbm]);
        }
        if (!empty($htmc)) {
            array_push($where, ['shtmc', 'like', '%' . $htmc . '%']);
        }
        if (!empty($htlx)) {
            array_push($where, ['shtlb', '=', $htlx]);
        }
        if (!empty($qymc)) {
            array_push($where, ['t_qyjbxx.sqymc', 'like', '%' . $qymc . '%']);
        }
        if (!empty($fzr)) {
            array_push($where, ['swyfwfzr', 'like', '%' . $fzr . '%']);
        }
        if (!empty($fzrlxdh)) {
            array_push($where, ['swyfwfzrlxdh', '=', $fzrlxdh]);
        }
        if (!empty($startdate)) {
            array_push($where, ['dhtkssj', '>', $startdate]);
        }
        if (!empty($enddate)) {
            array_push($where, ['dhtjssj', '<', $enddate]);
        }

        $hts = DB::table($this->tbName)
            ->join('t_qyjbxx', 't_qyjbxx.id', '=', 't_xm_ba_ht.qyid')
            ->where($where)
            ->select($select)
            ->paginate($size);
        return $hts;
    }

    public function getHtByXmid($xmId, $size)
    {
        $hts = DB::table($this->tbName)
            ->where('xmid', '=', $xmId)
            ->paginate($size);
        return $hts;
    }


    public function getHtById($htid)
    {
        $ht = DB::table($this->tbName)
            ->join('t_xm_jbxx', 't_xm_ba_ht.xmid' ,'=','t_xm_jbxx.id')
            ->join('t_qyjbxx', 't_qyjbxx.id', '=', 't_xm_ba_ht.qyid')
            ->where('t_xm_ba_ht.id', '=', $htid)
            ->select('t_xm_jbxx.sxmmc','t_qyjbxx.sqymc', 't_xm_ba_ht.*')
            ->first();
        return $ht;
    }

    public function searchHtForXm($xmid, $htmc, $htlx, $startdate, $enddate, $size)
    {
        $where = [];

        if (!empty($htmc)) {
            array_push($where, ['shtmc', 'like', '%' . $htmc . '%']);
        }
        if (!empty($htlx)) {
            array_push($where, ['shtlb', '=', $htlx]);
        }
        if (!empty($startdate)) {
            array_push($where, ['dhtkssj', '>', $startdate]);
        }
        if (!empty($enddate)) {
            array_push($where, ['dhtjssj', '<', $enddate]);
        }

        $hts = DB::table($this->tbName)
            ->join('t_qyjbxx', 't_qyjbxx.id', '=', 't_xm_ba_ht.qyid')
            ->where('xmid', '=', $xmid)
            ->where($where)
            ->select('t_xm_ba_ht.id as id', 'shtmc', 'shtlb', 't_qyjbxx.sqymc', 'swyfuqy','swyfwfzr', 'swyfwfzrlxdh', 'dhtkssj', 'dhtjssj','t_xm_ba_ht.ffj',$this->tbName.'.sstatus')
            ->paginate($size);
        return $hts;
    }

    public function submitHt($id)
    {
        $num = DB::table($this->tbName)->where([
            ['id', '=', $id],
            ['sstatus', '=', '暂存']
        ])->update(['sstatus' => '提交']);
        return $num == 0;
    }

    public function deleteHt($id)
    {
        $res = DB::table($this->tbName)->where('id', $id)->first();
        if ($res != null && $res->sstatus != '提交'){
            $this->delete($id);
            return true;
        }
        return false;
    }

    public function getHtEditStatus($id)
    {
        $status =  DB::table($this->tbName)->where('id',$id)->select('sstatus')->first();
        if($status!=null && $status->sstatus === '提交')
            return false;
        return true;
    }

    public function searchHtForFgj($sxmmc,$shtmc,$shtlb,$dhtkssj,$dhtjssj,$size)
    {
        $where = [];

        $where[] = [
            $this->tbName.'.sstatus', '=', '提交'
        ];

        if (!empty($sxmmc)) {
            array_push($where, ['sxmmc', 'like', '%' . $sxmmc . '%']);
        }
        if (!empty($shtmc)) {
            array_push($where, ['shtmc', 'like', '%' . $shtmc . '%']);
        }
        if (!empty($shtlb)) {
            array_push($where, ['shtlb', 'like', '%' . $shtlb . '%']);
        }
        if (!empty($dhtkssj)) {
            array_push($where, ['dhtjssj', '>=', $dhtkssj]);
        }
        if (!empty($dhtjssj)) {
            array_push($where, ['dhtkssj', '<=', $dhtjssj]);
        }
        $hts = DB::table($this->tbName)
            ->join('t_xm_jbxx', 't_xm_ba_ht.xmid' ,'=','t_xm_jbxx.id')
            ->join('t_qyjbxx', 't_qyjbxx.id', '=', 't_xm_ba_ht.qyid')
            ->where($where)
            ->select('t_xm_ba_ht.id as id','sxmmc' , 'shtmc', 'shtlb', 't_qyjbxx.sqymc', 'swyfuqy','swyfwfzr', 'swyfwfzrlxdh', 'dhtkssj', 'dhtjssj')
            ->paginate($size);
        return $hts;
    }
}