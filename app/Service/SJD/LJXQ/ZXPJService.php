<?php

namespace App\Service\SJD\LJXQ;

use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class ZXPJService
{
    //

    public function create($data){
//        $data['id']=SqlTool::makeUUID();
        DB::table('t_ywh_ljxq_zxpj_df')
            ->where([
                ['szxpjid','=',$data['szxpjid']],
                ['syzid','=',$data['syzid']]
            ])
            ->update($data);
    }

    public function hasDF($userId,$szxpjid){
        $res = DB::table('t_ywh_ljxq_zxpj_df')
            ->where([
                ['syzid','=',$userId],
                ['szxpjid','=',$szxpjid]
            ])->first();
        return $res;
    }
}
