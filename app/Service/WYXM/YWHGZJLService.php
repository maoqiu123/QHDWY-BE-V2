<?php

namespace App\Service\WYXM;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class YWHGZJLService extends BaseService
{

    protected $tbName = 't_ywh_gzjl';

    /**
     * 房管局端用查询
     * @param $xmmc
     * @param $startdate
     * @param $enddate
     * @param $size
     * @param array $select
     * @return mixed
     */
    public function search($xmmc, $startdate, $enddate, $size, $select = ['*'])
    {
        $where = [];
        if (!empty($xmmc)) {
            array_push($where, ['t_xm_jbxx.sxmmc', 'like', '%' . $xmmc . '%']);
        }
        if (!empty($startdate)) {
            array_push($where, ['t_ywh_gzjl.dhyrq', '>', $startdate]);
        }
        if (!empty($enddate)) {
            array_push($where, ['t_ywh_gzjl.dhyrq', '<', $enddate]);
        }
        $data = DB::table($this->tbName)
            ->join('t_ywh_jbxx', 't_ywh_jbxx.id', '=', 't_ywh_gzjl.ywhid')
            ->join('t_xm_jbxx', 't_xm_jbxx.id', '=', 't_ywh_jbxx.xmid')
            ->where($where)
            ->select($select)
            ->orderBy('t_ywh_gzjl.dhyrq','desc')
            ->paginate($size);
        return $data;
    }

    public function searchForYwh($ywhid, $startdate, $enddate, $status, $size, $select = ['*'])
    {
        $where = [];
        array_push($where, ['ywhid', '=', $ywhid]);
        if (!empty($startdata)) {
            array_push($where, ['t_ywh_gzjl.dhyrq', '>', $startdate]);
        }
        if (!empty($enddata)) {
            array_push($where, ['t_ywh_gzjl.dhyrq', '<', $enddate]);
        }
        if (!empty($status)) {
            array_push($where, ['t_ywh_gzjl.sstatus', '=', $status]);
        }
        $data = DB::table($this->tbName)
            ->where($where)
            ->select($select)
            ->paginate($size);
        return $data;
    }

    /**
     * 第一次暂存
     * @param $data
     */
    public function saveGzjl($data)
    {
        $this->create($data);
    }

    /**
     * 多次暂存更新
     * @param $id
     * @param $data
     */
    public function updateGzjl($id, $data)
    {
        $this->update($id, $data);
    }

    /**
     * 提交
     * @param $id
     * @param $data
     * @return bool
     */
    public function submitGzjl($id, $data)
    {
        $num = DB::table($this->tbName)->where([
            ['id', '=', $id],
            ['sstatus', '=', '暂存']
        ])->update($data);
        return $num == 0;
    }
}