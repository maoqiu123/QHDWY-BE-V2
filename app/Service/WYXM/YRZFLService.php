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

class YRZFLService extends BaseService
{
    protected $tbName = 't_xm_yrzxx_lbfl';

    /**
     * 搜索已入住分类
     */
    public function search($sxxbh, $irzqs, $sghlx, $sfwlx, $size)
    {
        $where = [];
        if ($sxxbh !== null && $sxxbh !== '') {
            array_push($where, ['sxxbh', '=', $sxxbh]);
        }

        if ($irzqs !== null && $irzqs !== 0) {
            array_push($where, ['irzqs', '=', $irzqs]);
        }

        if ($sghlx !== null && $sghlx !== '') {
            array_push($where, ['sghlx', '=', $sghlx]);
        }

        if ($sfwlx !== null && $sfwlx !== null) {
            array_push($where, ['sfwlx', '=', $sfwlx]);
        }

        $data = DB::table($this->tbName)
            ->where($where)
            ->paginate($size);
        return $data;
    }
}