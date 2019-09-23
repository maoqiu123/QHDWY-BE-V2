<?php

namespace App\Service\SJD\LJXQ;

use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ZGSQService
{
    //投票
    public function create($data)
    {
//        $data['id']=SqlTool::makeUUID();
        DB::table('t_ywh_ljxq_zgsq_tp')
            ->where([
                ['szgsqid','=',$data['szgsqid']],
                ['syzid','=',$data['syzid']]
            ])
            ->update($data);
    }
    //显示我的投票
    public function showMyTp($userId){
        $data = DB::table('t_ywh_ljxq_zgsq_tp')
            ->where('syzid',$userId)
            ->first();
        return $data;
    }

    public function isYzHasTp($userId,$zgsqId){
        $res = DB::table('t_ywh_ljxq_zgsq_tp')
            ->where([
                ['syzid','=',$userId],
                ['szgsqid','=',$zgsqId]
            ])->first();
        return $res;
    }
}
