<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/22
 * Time: 下午9:06
 */

namespace App\Service\XCKP;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class JCDXService extends BaseService
{
    protected $tbName = 't_xckp_jcdx';

    public function searchInspectionMajorTerm($taskId,int $size)
    {
        $where = [];
        if ($taskId !== null) {
            array_push($where, ['srwid', '=', $taskId]);
        }

        $data = DB::table($this->tbName)
            ->where($where)
            ->paginate($size);
        return $data;
    }

    public function deleteInspectionMajorTerm($id)
    {
        $res = DB::table('t_xckp_jcbz')->where('t_xckp_jcbz.sdxid',$id)
            ->leftjoin('t_xckp_qyzj','t_xckp_qyzj.sjcbzid','=','t_xckp_jcbz.id')
            ->leftjoin('t_xckp_jcjl','t_xckp_jcjl.sjcbzid','=','t_xckp_jcbz.id')
            ->count();
        if($res > 0)
            return false;
        $this->delete($id);
        return true;
    }




}