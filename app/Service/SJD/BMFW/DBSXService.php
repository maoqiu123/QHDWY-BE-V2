<?php

namespace App\Service\SJD\BMFW;

use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DBSXService
{
    public function getTpbjInfo($tpid)
    {
        $tpbj = DB::table('t_yz_tpbj')
            ->where('id', $tpid)
            ->first();
        return $tpbj;
    }

    public function getTpbjTable($tpid, $xmid, $yzid)
    {
        $tpbj = DB::table('t_yz_tpbj')
            ->where('t_yz_tpbj.id', $tpid)
            ->where('t_yz_tpbj.sxmid', $xmid)
            ->where('t_yz_tpbj.sstatus', '提交')
            ->leftjoin('t_yz_tpbj_bjjg', function ($join) use ($yzid) {
                $join->on('t_yz_tpbj_bjjg.stpbjid', '=', 't_yz_tpbj.id')
                    ->where('t_yz_tpbj_bjjg.syzid', $yzid);
            })
            ->select('t_yz_tpbj.*', 't_yz_tpbj.id as tpid', 't_yz_tpbj_bjjg.*')
//            ->orderBy('t_yz_tpbj.dfbrq', 'desc')
            ->first();
        $tpbj = SqlTool::getFileJsonDecode($tpbj);
        return $tpbj;
    }

    public function getTpbjRes($bjid, $yzid)
    {
        $tpbjInfo = DB::table('t_yz_tpbj')
            ->where('t_yz_tpbj.id', $bjid)
            ->Leftjoin('t_yz_tpbj_bjjg', 't_yz_tpbj_bjjg.stpbjid', '=', 't_yz_tpbj.id')
            ->where('t_yz_tpbj_bjjg.syzid', $yzid)
            ->select('t_yz_tpbj.*', 't_yz_tpbj_bjjg.*', 't_yz_tpbj_bjjg.id as bjjgid')
            ->orderBy('t_yz_tpbj.dfbrq', 'desc')
            ->first();
        $tpbjInfo = SqlTool::getFileJsonDecode($tpbjInfo);
        return $tpbjInfo;
    }

    public function voteTpbj($tpid, $data)
    {
        $time = Carbon::now();
        $data = array_merge($data, [
            'sstatus' => '提交',
            'ssfzd' => '否',
            'dtprq' => $time,
            'dtxrq' => $time
        ]);
        DB::table('t_yz_tpbj_bjjg')->where('id', $tpid)->update($data);
        return $tpid;
    }

    public function getZlpjInfo($pjid)
    {
        $zlpj = DB::table('')
            ->where('id', $pjid)
            ->first();
        return $zlpj;
    }

    public function getZlpjTable($pjid, $xmid, $yzid)
    {
        $zlpj = DB::table('t_yz_zlpj')
            ->where('t_yz_zlpj.id', $pjid)
            ->where('t_yz_zlpj.sxmid', $xmid)
            ->where('t_yz_zlpj.sstatus', '提交')
            ->leftjoin('t_yz_zlpj_pjjg', function ($join) use ($yzid) {
                $join->on('t_yz_zlpj_pjjg.szlpjid', '=', 't_yz_zlpj.id')
                    ->where('t_yz_zlpj_pjjg.syzid', $yzid);
            })
            ->select('t_yz_zlpj.*', 't_yz_zlpj.id as pjid', 't_yz_zlpj_pjjg.*')
            ->get();
        return $zlpj;
    }

    public function getZlpjContent()
    {
        $zlpjInfo = DB::table('t_code')
            ->where('dmlb', 'zlpj')
            ->get();
        return $zlpjInfo;
    }

    public function ifZlpj($pjid, $yzid)
    {
        $zlpjInfo = DB::table('t_yz_zlpj_pjjg')
            ->where([
                ['t_yz_zlpj_pjjg.szlpjid', $pjid],
                ['t_yz_zlpj_pjjg.syzid', $yzid]
            ])
            ->count();
        $res = false;
        if($zlpjInfo > 0)
        {
            $count = 0;
            foreach ($zlpjInfo as $zlpj)
            {
                if($zlpj['spjyj'] != null)
                    $count++;
            }
            if($count == 5)
                $res = true;
        }
        return $res ;
    }

    public function getZlpjRes($bjid, $yzid)
    {
        $zlpjInfo = DB::table('t_yz_zlpj')
            ->where('t_yz_zlpj.id', $bjid)
            ->Leftjoin('t_yz_zlpj_pjjg', 't_yz_zlpj_pjjg.szlpjid', '=', 't_yz_zlpj.id')
            ->where('t_yz_zlpj_pjjg.syzid', $yzid)
            ->select('t_yz_zlpj.*', 't_yz_zlpj_pjjg.*', 't_yz_zlpj_pjjg.id as bjjgid')
            ->orderBy('t_yz_zlpj.dfbrq', 'desc')
            ->get();
        return $zlpjInfo;
    }

    public function voteZlpj($userid, $pjid, $datas)
    {
        $time = Carbon::now();
        foreach ($datas as $data) {
            $res = array_merge($data, [
                'sstatus' => '提交',
                'ssfzd' => '否',
                'dpjrq' => $time,
                'dtxrq' => $time
            ]);
            DB::table('t_yz_zlpj_pjjg')
                ->where([
                    ['syzid', $userid],
                    ['szlpjid', $pjid],
                    ['spjxm', $data['spjxm']],
                ])
                ->update($res);
        }
    }

    public function getDbsxList($xmid, $yzid)
    {
        $data = [];

        $zlpj = DB::table('t_yz_zlpj')
            ->where('t_yz_zlpj.sxmid', $xmid)
            ->where('t_yz_zlpj.sstatus', '提交')
            ->join('t_yz_zlpj_pjjg', 't_yz_zlpj_pjjg.szlpjid','=','t_yz_zlpj.id')
            ->where('t_yz_zlpj_pjjg.syzid', $yzid)
            //此处选择其一评价结果为代表，改代码表时要改此处
            ->where('t_yz_zlpj_pjjg.spjxm', '综合服务')
            ->select('t_yz_zlpj.*', 't_yz_zlpj_pjjg.*', 't_yz_zlpj_pjjg.id as pjjgid','t_yz_zlpj.id as id',DB::raw("'01' as type"))
            ->orderBy('t_yz_zlpj.dfbrq', 'desc')
            ->get()
            ->toArray();

        $tpbj = DB::table('t_yz_tpbj')
            ->where('t_yz_tpbj.sxmid', $xmid)
            ->where('t_yz_tpbj.sstatus', '提交')
            ->join('t_yz_tpbj_bjjg', 't_yz_tpbj_bjjg.stpbjid','=','t_yz_tpbj.id')
            ->where('t_yz_tpbj_bjjg.syzid', $yzid)
            ->select('t_yz_tpbj.*', 't_yz_tpbj_bjjg.*', 't_yz_tpbj_bjjg.id as bjjgid','t_yz_tpbj.id as id',DB::raw("'02' as type"))
            ->orderBy('t_yz_tpbj.dfbrq', 'desc')
            ->get()
            ->toArray();


        $zgsq = DB::table('t_ywh_ljxq_zgsq')
            ->where('t_ywh_ljxq_zgsq.sxmid', $xmid)
            ->where('t_ywh_ljxq_zgsq.sstatus', '提交')
            ->join('t_ywh_ljxq_zgsq_tp', 't_ywh_ljxq_zgsq_tp.szgsqid','=','t_ywh_ljxq_zgsq.id')
            ->where('t_ywh_ljxq_zgsq_tp.syzid', $yzid)
            ->select('t_ywh_ljxq_zgsq.*', 't_ywh_ljxq_zgsq_tp.*', 't_ywh_ljxq_zgsq_tp.id as sqjgid','t_ywh_ljxq_zgsq.id as id',DB::raw("'03' as type"))
            ->orderBy('t_ywh_ljxq_zgsq.dfbrq', 'desc')
            ->get()
            ->toArray();

        $gzfa = DB::table('t_ywh_ljxq_gzfa')
            ->where('t_ywh_ljxq_gzfa.sxmid', $xmid)
            ->where('t_ywh_ljxq_gzfa.sstatus', '提交')
            ->join('t_ywh_ljxq_gzfa_qr', 't_ywh_ljxq_gzfa_qr.sgzfaid','=','t_ywh_ljxq_gzfa.id')
            ->where('t_ywh_ljxq_gzfa_qr.syzid', $yzid)
            ->select('t_ywh_ljxq_gzfa.*', 't_ywh_ljxq_gzfa_qr.*', 't_ywh_ljxq_gzfa_qr.id as fajgid','t_ywh_ljxq_gzfa.id as id', DB::raw("'04' as type"))
            ->orderBy('t_ywh_ljxq_gzfa.dfbrq', 'desc')
            ->get()
            ->toArray();

        $zxys = DB::table('t_ywh_ljxq_zxys')
            ->where('t_ywh_ljxq_zxys.sxmid', $xmid)
            ->where('t_ywh_ljxq_zxys.sstatus', '提交')
            ->join('t_ywh_ljxq_zxys_qr', 't_ywh_ljxq_zxys_qr.szxysid','=','t_ywh_ljxq_zxys.id')
            ->where('t_ywh_ljxq_zxys_qr.syzid', $yzid)
            ->select('t_ywh_ljxq_zxys.*', 't_ywh_ljxq_zxys_qr.*', 't_ywh_ljxq_zxys_qr.id as ysjgid','t_ywh_ljxq_zxys.id as id',DB::raw("'05' as type"))
            ->orderBy('t_ywh_ljxq_zxys.dfbrq', 'desc')
            ->get()
            ->toArray();

        $glms = DB::table('t_ywh_ljxq_glms')
            ->where('t_ywh_ljxq_glms.sxmid', $xmid)
            ->where('t_ywh_ljxq_glms.sstatus', '提交')
            ->join('t_ywh_ljxq_glms_qr', 't_ywh_ljxq_glms_qr.sglmsid','=','t_ywh_ljxq_glms.id')
            ->where('t_ywh_ljxq_glms_qr.syzid', $yzid)
            ->select('t_ywh_ljxq_glms.*', 't_ywh_ljxq_glms_qr.*', 't_ywh_ljxq_glms_qr.id as msjgid','t_ywh_ljxq_glms.id as id', DB::raw("'06' as type"))
            ->orderBy('t_ywh_ljxq_glms.dfbrq', 'desc')
            ->get()
            ->toArray();

        $zxpj = DB::table('t_ywh_ljxq_zxpj')
            ->where('t_ywh_ljxq_zxpj.sxmid', $xmid)
            ->where('t_ywh_ljxq_zxpj.sstatus', '提交')
            ->join('t_ywh_ljxq_zxpj_df', 't_ywh_ljxq_zxpj_df.szxpjid','=','t_ywh_ljxq_zxpj.id')
            ->where('t_ywh_ljxq_zxpj_df.syzid', $yzid)
            ->select('t_ywh_ljxq_zxpj.*', 't_ywh_ljxq_zxpj_df.*', 't_ywh_ljxq_zxpj_df.id as msjgid','t_ywh_ljxq_zxpj.id as id', DB::raw("'07' as type"))
            ->orderBy('t_ywh_ljxq_zxpj.dfbrq', 'desc')
            ->get()
            ->toArray();
        //todo 置顶
        foreach ($zlpj as $item)
            $data[] = $item;
        foreach ($tpbj as $item)
            $data[] = $item;
        foreach ($zgsq as $item)
            $data[] = $item;
        foreach ($gzfa as $item)
            $data[] = $item;
        foreach ($zxys as $item)
            $data[] = $item;
        foreach ($glms as $item)
            $data[] = $item;
        foreach ($zxpj as $item)
            $data[] = $item;

//        uksort($data, function ($a, $b) {
//            return $a->id - $b->id;
//        });
//        dd($data);
        return $data;
    }
}