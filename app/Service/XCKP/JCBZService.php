<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/22
 * Time: 下午10:02
 */

namespace App\Service\XCKP;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class JCBZService extends BaseService
{
    protected $tbName = 't_xckp_jcbz';


    public function getInspectionMajorTermByTaskId($taskId)
    {
        $data = DB::table('t_xckp_jcdx')
            ->where('srwid',$taskId)
            ->select('id','sdxmc')
            ->get();
        return $data;
    }

    public function searchInspectionStandard($taskId,$majorTermId,int $size)
    {
        $where = [];
        if ($taskId !== null) {
            array_push($where, ['srwid', '=', $taskId]);
        }

        if ($majorTermId !== null) {
            array_push($where, ['sdxid', '=', $majorTermId]);
        }

        $data = DB::table($this->tbName)
            ->where($where)
            ->paginate($size);
        return $data;
    }

    public function deleteInspectionStandard($id)
    {
        $resSelf = DB::table('t_xckp_qyzj')
            ->where('sjcbzid','=',$id )
            ->count();
        $resSelf += DB::table('t_xckp_jcjl')
            ->where('sjcbzid','=',$id )
            ->count();

        if($resSelf > 0)
            return false;
        $this->delete($id);
        return true;
    }
}