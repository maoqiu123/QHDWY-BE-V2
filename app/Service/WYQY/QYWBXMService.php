<?php
namespace App\Service\WYQY;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class QYWBXMService extends BaseService
{

    protected $tbName='t_qyjbxx_wbxm';

    public function searchWbxm($shehui , $enterpriseName, $xmmc, $zldz, int $page)
    {
        $where=[];
        if ($shehui !== null) {
            array_push($where, ['t_qyjbxx.sshxydm', '=', $shehui]);
        }
        if ($enterpriseName !== null) {
            array_push($where, ['t_qyjbxx.sqymc', 'like', '%' . $enterpriseName . '%']);
        }
        if ($xmmc !== null) {
            array_push($where, ['t_qyjbxx_wbxm.sxmmc', 'like', '%'.$xmmc.'%']);
        }
        if ($zldz !== null) {
            array_push($where, ['t_qyjbxx_wbxm.szldz', 'like', '%'.$zldz.'%']);
        }

        $data = DB::table($this->tbName)
            ->join('t_qyjbxx','t_qyjbxx_wbxm.qyid','=','t_qyjbxx.id')
            ->select('t_qyjbxx_wbxm.id','t_qyjbxx.sqymc','t_qyjbxx.sshxydm','t_qyjbxx_wbxm.sxmmc','t_qyjbxx_wbxm.szldz','t_qyjbxx_wbxm.sghlx','t_qyjbxx_wbxm.njzmj','t_qyjbxx_wbxm.ifwzts','t_qyjbxx_wbxm.dhtqsrq','t_qyjbxx_wbxm.dhtzzrq','t_qyjbxx_wbxm.njcwyf')
            ->where($where)
            ->paginate($page);
        return $data;
    }
    public function deleteWbxm($id): bool
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
            ->select('id','sxmmc','szldz','sghlx','njzmj','ifwzts','dhtqsrq','dhtzzrq','njcwyf','nsbssyhfy','nwyfndsjl')
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