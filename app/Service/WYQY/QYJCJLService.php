<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/17
 * Time: 下午8:50
 */

namespace App\Service\WYQY;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class QYJCJLService extends BaseService
{
    protected $tbName='t_qyjbxx_jcjl';

    public function searchZzxx($enterprise_code , $enterpriseName, int $size)
    {
        $where=[];
        if ($enterprise_code !== null) {
            array_push($where, ['t_qyjbxx.sshxydm', '=', $enterprise_code]);
        }
        if ($enterpriseName !== null) {
            array_push($where, ['t_qyjbxx.sqymc', 'like', '%' . $enterpriseName . '%']);
        }


        $datas = DB::table($this->tbName)
            ->join('t_qyjbxx',$this->tbName.'.qyid','=','t_qyjbxx.id')
            ->select($this->tbName.'.id','t_qyjbxx.sshxydm','t_qyjbxx.sqymc',$this->tbName.'.slx',$this->tbName.'.djcrq',$this->tbName.'.sjcnr',$this->tbName.'.sjcjb',$this->tbName.'.sjcjg',$this->tbName.'.ffj')
            ->where($where)
            ->paginate($size);

        foreach ($datas as $data) {
            $data->ffj = json_decode($data->ffj);
            if ($data->ffj != null) {
                foreach ($data->ffj as $key => $value)
                    $data->ffj->$key = json_decode($value);
            }
        }
        return $datas;
    }
    public function deleteQyjcjl($id): bool
    {
        $data = $this->show([
            ['id', '=', $id]
        ]);
   //     $data = $data->first();
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
            ->select('id','slx','djcrq','sjcnr','sjcjg','sjcjb','ffj')
            ->paginate($size);
        foreach ($datas as $data) {
            $data->ffj = json_decode($data->ffj);
            if ($data->ffj != null) {
                foreach ($data->ffj as $key => $value)
                    $data->ffj->$key = json_decode($value);
            }
        }
        return $datas;
    }
}