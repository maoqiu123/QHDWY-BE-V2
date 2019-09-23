<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/17
 * Time: 上午10:48
 */

namespace App\Service\WYQY;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class QYZZXXService extends BaseService
{
    protected $tbName='t_qyjbxx_zzxx';

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
            ->select($this->tbName.'.id','t_qyjbxx.sqymc',$this->tbName.'.szzdj',$this->tbName.'.dqdrq',$this->tbName.'.ffj','t_qyjbxx.sshxydm')
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

    public function deleteQyzzxx($id): bool
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
            ->select('id','szzdj','dqdrq','ffj')
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