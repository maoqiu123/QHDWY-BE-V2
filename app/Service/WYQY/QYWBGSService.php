<?php

namespace App\Service\WYQY;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class QYWBGSService extends BaseService
{

    protected $tbName = 't_qyjbxx_wbgs';


    public function searchWbgs($enterpriseCode, $sqymc, $size)
    {
        $where = [];
        if (isset($enterpriseCode) && $enterpriseCode !== '') {
            array_push($where, ['t_qyjbxx.sshxydm', '=', $enterpriseCode]);
        }

        if (isset($sqymc) && $sqymc !== '') {
            array_push($where, ['t_qyjbxx.sqymc', 'like', '%' . $sqymc . '%']);
        }

        $wbgs = DB::table($this->tbName)
            ->leftJoin('t_qyjbxx', 't_qyjbxx.id', '=', 't_qyjbxx_wbgs.qyid')
            ->select('t_qyjbxx_wbgs.id', 't_qyjbxx.id as qyid', 't_qyjbxx.sqybm', 't_qyjbxx.sqymc', 't_qyjbxx_wbgs.swbgsmc',
                't_qyjbxx_wbgs.sfddbr', 't_qyjbxx_wbgs.dclrq', 't_qyjbxx_wbgs.iyyqx', 't_qyjbxx_wbgs.nzczj',
                't_qyjbxx_wbgs.slxdh', 't_qyjbxx_wbgs.swbxm','t_qyjbxx.sshxydm')
            ->where($where)
            ->paginate($size);
        return $wbgs;
    }

    public function showWbgs($id)
    {
        $wbgs = DB::table($this->tbName)
            ->where($this->tbName.'.id','=',$id)
            ->join('t_qyjbxx',$this->tbName.'.qyid', '=', 't_qyjbxx.id')
            ->select('t_qyjbxx.sqymc',$this->tbName.'.*')
            ->first();
        return $wbgs;
    }
    public function serarchForQy($id,$size)
    {
        $datas=DB::table($this->tbName)
            ->where('qyid',$id)
            ->select('id','swbgsmc','sfddbr','dclrq','iyyqx','nzczj','slxdh','swbxm','sshxydm')
            ->paginate($size);
//        foreach ($datas as $data) {
//            $data->ffj = json_decode($data->ffj);
//            if ($data->ffj != null) {
//                foreach ($data->ffj as $key => $value)
//                    $data->ffj->$key = json_decode($value);
//            }
//        }
        return $datas;
    }
}