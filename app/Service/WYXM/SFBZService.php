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

class SFBZService extends BaseService
{
    protected $tbName = 't_xm_sfbz';

    /**
     * 搜索收费标准
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
    public function getXmbh($id)
    {
        $xmbh=DB::table('t_xm_jbxx')->where('id',$id)->value('sxmbh');
        return $xmbh;
    }
    public function getXmmc($id)
    {
        $xmmc=DB::table('t_xm_jbxx')->where('id',$id)->value('sxmmc');
        return $xmmc;
    }
    public function createXm($data)
    {
        DB::table($this->tbName)->insert($data);
    }
    public function searchByXmid($xmid)
    {
        $data=DB::table($this->tbName)
            ->where('xmid',$xmid)
            ->first();
        return $data;
    }
    public function searchById($id)
    {
        $data=DB::table($this->tbName)
            ->where('id',$id)
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

    public function searchSfbz($xmid,$sflb,$sfxm,$page)
    {
        $where = [];
        if (isset($sflb)) {
            array_push($where, ['ssflb', '=', $sflb]);
        }
        if (isset($sfxm)) {
            array_push($where, ['ssfxm', '=', $sfxm]);
        }

        $data = DB::table($this->tbName)
            ->where('sxmid',$xmid)
            ->where($where)
            ->paginate($page);
        return $data;
    }

    public function getStatus($id)
    {
        $res = DB::table($this->tbName)
            ->where('id',$id)
            ->value('sstatus');
        return $res;
    }

    public function getSfbz($id)
    {
        $data=DB::table($this->tbName)->where('id',$id)->first();
        return $data;
    }
}