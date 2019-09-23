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

class ZYGSService extends BaseService
{
    protected $tbName = 't_xm_rypz_zygs';

    /**
     * 搜索专业公司
     */
    public function search($sxmbh, $swblb, $sgxmc, $size)
    {
        $where = [];
        if ($sxmbh !== null && $sxmbh !== '') {
            array_push($where, ['sxmbh', '=', $sxmbh]);
        }

        if ($sgxmc !== null && $sgxmc !== 0) {
            array_push($where, ['sgxmc', 'like', '%' . $sgxmc . '%']);
        }

        if ($swblb !== null && $swblb !== '') {
            array_push($where, ['swblb', '=', $swblb]);
        }

        $data = DB::table($this->tbName)
            ->where($where)
            ->paginate($size);
        return $data;
    }
}