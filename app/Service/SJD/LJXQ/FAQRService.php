<?php

namespace App\Service\SJD\LJXQ;

use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class FAQRService
{
    // 方案确认
    public function create($data){
//        $data['id']=SqlTool::makeUUID();
        DB::table('t_ywh_ljxq_gzfa_qr')
            ->where([
                ['sgzfaid','=',$data['sgzfaid']],
                ['syzid','=',$data['yzid']]
            ])
            ->update($data);
    }
    // 业主是否已确认
    public function hasQr($userId,$sgzfaId){
        $res = DB::table('t_ywh_ljxq_gzfa_qr')
            ->where([
                ['syzid','=',$userId],
                ['sgzfaid','=',$sgzfaId]
            ])->first();
        return $res;
    }

}
