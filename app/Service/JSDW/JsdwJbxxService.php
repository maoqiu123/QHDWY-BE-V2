<?php

namespace App\Service\JSDW;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class JsdwJbxxService extends BaseService{

    protected $tbName= "t_jsdw_jbxx";

    public function showById($jsdwId){
        $jsdw = DB::table($this->tbName)
            ->where('id',$jsdwId)
            ->first();
        return $jsdw;
    }


}