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

class RYPZService extends BaseService
{
    protected $tbName = 't_xm_rypz';

    /**
     * 搜索人员配置
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

    public function update($xmid,$info)
    {
        DB::table($this->tbName)->where('xmid',$xmid)->update($info);
    }

    public function searchByXmid($xmid)
    {
        $data=DB::table($this->tbName)
            ->where('xmid',$xmid)
            ->get();
        return $data;
    }
    public function showById($id)
    {
        $data=DB::table($this->tbName)
            ->where('id',$id)
            ->get();
        return $data;
    }
}