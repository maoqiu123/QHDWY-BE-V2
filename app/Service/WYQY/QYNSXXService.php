<?php

namespace App\Service\WYQY;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class QYNSXXService extends  BaseService {
    protected $tbName='t_qyjbxx_nsxx';

    public function searchNsxx($enterprise_code , $enterpriseName, $startNd, $endNd, int $page)
    {
        $where=[];
        if ($enterprise_code !== null) {
            array_push($where, ['t_qyjbxx.sshxydm', '=', $enterprise_code]);
        }
        if ($enterpriseName !== null) {
            array_push($where, ['t_qyjbxx.sqymc', 'like', '%' . $enterpriseName . '%']);
        }
        if ($startNd !== null) {
            array_push($where, ['t_qyjbxx_nsxx.nnd', '>=', $startNd]);
        }
        if ($endNd !== null) {
            array_push($where, ['t_qyjbxx_nsxx.nnd', '<=', $endNd]);
        }

        $data = DB::table($this->tbName)
            ->join('t_qyjbxx','t_qyjbxx_nsxx.qyid','=','t_qyjbxx.id')
            ->select('t_qyjbxx_nsxx.id','t_qyjbxx.sshxydm','t_qyjbxx.sqymc','t_qyjbxx_nsxx.nnd','t_qyjbxx_nsxx.nnyysr','t_qyjbxx_nsxx.nnsje','t_qyjbxx_nsxx.nzyywsr','t_qyjbxx_nsxx.nzyyjsj','t_qyjbxx_nsxx.nqtsr','t_qyjbxx_nsxx.nqtyjsj','t_qyjbxx_nsxx.nyylr','t_qyjbxx_nsxx.nlrze')
            ->where($where)
            ->paginate($page);
        return $data;
    }

    public function deleteNsxx($id): bool
    {
        $data = $this->show([
            ['id', '=', $id]
        ]);
        //      $data = $data->first();
        $this->delete($id);
        return true;
    }

    public function showByQy($id)
    {
        $data=DB::table($this->tbName)->where('id',$id)->first();
        return $data;
    }

    public function serarchForQy($id,$size)
    {
        $datas=DB::table($this->tbName)
            ->where('qyid',$id)
            ->select('id','nnd','nnyysr','nnsje','nzyywsr','nzyyjsj','nqtsr','nqtyjsj','nyylr','nlrze')
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