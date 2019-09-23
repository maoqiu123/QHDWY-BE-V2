<?php

namespace App\Service\SJD\LJXQ;

use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class ZXYSService
{
    //
    public function create($data){
//        $data['id']=SqlTool::makeUUID();
        DB::table('t_ywh_ljxq_zxys_qr')
            ->where([
                ['szxysid','=',$data['szxysid']],
                ['syzid','=',$data['syzid']]
            ])
            ->update($data);
    }

    public function hasQr($userId,$zxysId){
        $res = DB::table('t_ywh_ljxq_zxys_qr')
            ->where([
                ['szxysid','=',$zxysId],
                ['syzid','=',$userId]
            ])->first();
        return $res;
    }
}
