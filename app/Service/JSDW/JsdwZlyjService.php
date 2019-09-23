<?php

namespace App\Service\JSDW;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class JsdwZlyjService extends BaseService{

    protected $tbName = "t_jsdw_zlyj";
    /*************************************建设单位端******************************************************/
    public function searchByXmmc($xmmc,$jsdwId,$size){
        $zlyjs = DB::table($this->tbName)
            ->join('t_xm_jbxx','t_xm_jbxx.id','=','t_jsdw_zlyj.sxmid')
            ->where('t_xm_jbxx.sxmmc','like','%'.$xmmc.'%')
            ->where($this->tbName.'.sjsdwid','=',$jsdwId)
            ->select('t_xm_jbxx.sxmmc',$this->tbName.'.*')
            ->paginate($size);
        return $zlyjs;
    }
    public function showById($zlyjId){
        $zlyj = DB::table($this->tbName)
            ->where('id','=',$zlyjId)
            ->first();
        return $zlyj;
    }
    public function deleteById($zlyjId){

        return DB::table($this->tbName)
            ->where('id',$zlyjId)
            ->where('sstatus','暂存')
            ->delete();
    }
    public function submitById($zlyjId){
        return DB::table($this->tbName)
            ->where('id',$zlyjId)
            ->where('sstatus','暂存')
            ->update([
                'sstatus'=>'提交'
            ]);
    }

    /**************************************************其它端**************************************************/

}