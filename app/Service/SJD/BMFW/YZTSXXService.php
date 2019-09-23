<?php

namespace App\Service\SJD\BMFW;

use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class YZTSXXService
{
    private $tbName = 't_yz_yztsxx';

    public function createTsxx($userId, $data)
    {
        $enterpriseId = SqlTool::getEnterpriseId($userId);

        $data = array_merge($data, [
            'id' => SqlTool::makeUUID(),
            'yzid' => $userId,
            'qyid' => $enterpriseId,
            'sslzt' => '未受理',
            'sstatus' => '提交'
        ]);
        DB::table($this->tbName)->insert($data);
    }

    public function showTsxxList($userId)
    {
        $Infos = DB::table($this->tbName)
            ->where('yzid', '=', $userId)
            ->select('id', 'stssx' , 'dtsrq', 'sslzt', 'sstatus')
            ->orderBy('dTsrq', 'desc')
            ->get();
        return $Infos;
    }

    public function showTsxx($id)
    {
        $info = DB::table($this->tbName)
            ->where('id',$id)
            ->first();
        return $info;
    }

    public function recallTsxx($id)
    {
        DB::table($this->tbName)
            ->where('id',$id)
            ->update([
                'sstatus' => '撤回'
            ]);
    }

    public function evaluateTsxx($id,$data)
    {
        $res = DB::table($this->tbName)->where('id',$id)->first();
        if($res == null || $res->sslzt !== "已办结")
            return false;
        DB::table($this->tbName)
            ->where('id',$id)
            ->update($data);
        return true;
    }
}