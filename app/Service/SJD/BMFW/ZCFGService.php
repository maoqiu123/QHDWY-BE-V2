<?php

namespace App\Service\SJD\BMFW;

use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ZCFGService
{
    private $tbName = 't_zcgf';

    public function searchZcfg($range, $keyword)
    {
        $where = [];
        $nowTime = Carbon::now();
        $beforeTime = Carbon::now();
        if ($range == '半年内') {
            $beforeTime = $beforeTime->subMonth(6);
        } else if ($range == '一年内') {
            $beforeTime = $beforeTime->subYear(1);
        } else {
            $beforeTime = '';
        }

        $where[] = ['dfbsj', '>=', $beforeTime];
        $where[] = ['dfbsj', '<=', $nowTime];
        if (!empty($keyword)) {
            $where[] = ['sbt', 'like', '%'.$keyword.'%'];
        }
        $where[] = ['sstatus', '=', '提交'];

        $zcfgList = DB::table($this->tbName)
            ->where($where)
            ->select('id', 'sbt', 'dfbsj', 'sfbr')
            ->get();
        return $zcfgList;
    }

    public function getZcfgById($id)
    {
        $zcfg = DB::table($this->tbName)
            ->where('id', $id)
            ->first();
        $zcfg = SqlTool::getFileJsonDecode($zcfg);
        return $zcfg;
    }
}
