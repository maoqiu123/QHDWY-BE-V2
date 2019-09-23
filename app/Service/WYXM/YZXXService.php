<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/21
 * Time: 下午1:49
 */

namespace App\Service\WYXM;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class YZXXService extends BaseService
{
    protected $tbName = 't_xm_yzxx';

    /**
     * 搜索业主信息
     */
    public function search($sxmbh, $size)
    {
        $where = [];
        if ($sxmbh !== null && $sxmbh !== '') {
            array_push($where, ['sxmbh', '=', $sxmbh]);
        }

        $data = DB::table($this->tbName)
            ->where($where)
            ->paginate($size);
        return $data;
    }

    public function getXmbh($id){
        $xmbh=DB::table('t_xm_jbxx')->where('id',$id)->value('sxmbh');
        return $xmbh;
    }

    public function searchByXmid($xmid)
    {
        $data=DB::table($this->tbName)
            ->where('xmid',$xmid)
            ->first();
        return $data;
    }

    public function getId($xmid)
    {
        $id=DB::table($this->tbName)
            ->where('xmid',$xmid)
            ->value('id');
        return $id;
    }

    public function isExist($xmid)
    {
        $flag=DB::table($this->tbName)
            ->where('xmid',$xmid)
            ->value('id');
        if($flag==null)return 0;
        else return 1;
    }
}