<?php

namespace App\Service\WYXM;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class YWHCYService extends BaseService
{


    protected $tbName = 't_ywh_cyjl';

    /**
     * 业委会端用查询
     * @param $ywhId
     * @param $sryzt
     * @param $sstatus
     * @return mixed
     */
    public function searchForYwh($ywhId, $sryzt, $sstatus, $size)
    {
        $where = [];
        array_push($where,['ywhid','=',$ywhId]);
        if (!empty($sryzt)){
            array_push($where,['sryzt','=',$sryzt]);
        }
        if (!empty($sstatus)){
            array_push($where,['sstatus','=',$sstatus]);
        }

        $cy = DB::table($this->tbName)
            ->where($where)
            ->paginate($size);
        return $cy;
    }

    public function getCyxxByCyId($cyid){
        $cyInfo = DB::table($this->tbName)->where('id',$cyid)->get();
        return $cyInfo;
    }

    public function getCyList($ywhid,$size)
    {
        $data=DB::table($this->tbName)
            ->where('ywhid',$ywhid)
            ->select('id','sxm','ssfzh','slxdh','syzw','drqksrq','sryzt','sstatus')
            ->orderBy('drqksrq','desc')
            ->paginate($size);
        return $data;
    }

    public function getstatus($id)
    {
        $status=DB::table($this->tbName)
                ->where('id',$id)
                ->value('sstatus');
        return $status;
    }
}