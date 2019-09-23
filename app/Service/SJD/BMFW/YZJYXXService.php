<?php

namespace App\Service\SJD\BMFW;

use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class YZJYXXService
{
    private $tbName = 't_yz_yzjyxx';

    public function createJyxx($userId, $data)
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

    public function showJyxxList($userId)
    {
        $Infos = DB::table($this->tbName)
            ->where('yzid', '=', $userId)
            ->select('id', 'sjysx' , 'djyrq', 'sslzt', 'sstatus')
            ->orderBy('djyrq', 'desc')
            ->get();
        return $Infos;
    }

    public function showJyxx($id)
    {
        $info = DB::table($this->tbName)
            ->where('id',$id)
            ->first();
        return $info;
    }

    public function recallJyxx($id)
    {
        DB::table($this->tbName)
            ->where('id',$id)
            ->update([
                'sstatus' => '撤回'
            ]);
    }

    public function evaluateJyxx($id,$data)
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