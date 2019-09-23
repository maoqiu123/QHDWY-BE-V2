<?php

namespace App\Service\SJD\BMFW;

use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class YZBXXXService
{
    private $tbName = 't_yz_yzbxxx';

    public function createBxxx($userId, $data)
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

    public function showBxxxList($userId)
    {
        $Infos = DB::table($this->tbName)
            ->where('yzid', '=', $userId)
            ->select('id', 'sbxsx' , 'dbxrq', 'sslzt', 'sstatus')
            ->orderBy('dbxrq', 'desc')
            ->get();
        return $Infos;
    }

    public function showBxxx($id)
    {
        $info = DB::table($this->tbName)
            ->where('id',$id)
            ->first();
        return $info;
    }

    public function recallBxxx($id)
    {
        DB::table($this->tbName)
            ->where('id',$id)
            ->update([
                'sstatus' => '撤回'
            ]);
    }

    public function evaluateBxxx($id,$data)
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
