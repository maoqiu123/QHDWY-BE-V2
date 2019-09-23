<?php

namespace App\Service\SJD\LJXQ;

use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class GLMSService
{
    //
    public function create($data){
//        $data['id']=SqlTool::makeUUID();

        DB::table('t_ywh_ljxq_glms_qr')
            ->where([
                ['sglmsid','=',$data['sglmsid']],
                ['syzid','=',$data['syzid']]
            ])
            ->update($data);
    }


    public function hasQr($userId,$glmsId){
        $res = DB::table('t_ywh_ljxq_glms_qr')
            ->where([
                ['syzid','=',$userId],
                ['sglmsid','=',$glmsId]
            ])->first();
        return $res;
    }

}
