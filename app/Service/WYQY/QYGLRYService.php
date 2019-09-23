<?php

namespace App\Service\WYQY;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class QYGLRYService extends BaseService
{
    protected $tbName='t_qyjbxx_glry';

    public function searchGlry($enterprise_code , $enterpriseName, $zhiwu, $name, int $size)
    {
        $where=[];
        if ($enterprise_code !== null) {
            array_push($where, ['t_qyjbxx.sshxydm', '=', $enterprise_code]);
        }
        if ($enterpriseName !== null) {
            array_push($where, ['t_qyjbxx.sqymc', 'like', '%' . $enterpriseName . '%']);
        }

        if ($zhiwu!==null){
            array_push($where,['t_qyjbxx_glry.sxrzw','=',$zhiwu]);
        }
        if ($name!==null){
            array_push($where,['t_qyjbxx_glry.sxm','like','%'.$name.'%']);
        }

        $data = DB::table($this->tbName)
            ->join('t_qyjbxx','t_qyjbxx_glry.qyid','=','t_qyjbxx.id')
            ->select('t_qyjbxx_glry.id','t_qyjbxx.sshxydm', 't_qyjbxx.sqymc','t_qyjbxx_glry.sxm','t_qyjbxx_glry.sxb','t_qyjbxx_glry.dcsrq','t_qyjbxx_glry.sxrzw','t_qyjbxx_glry.drzrq','t_qyjbxx_glry.szjhm','t_qyjbxx_glry.szc')
            ->where($where)
            ->paginate($size);
        return $data;
    }

    public function serarchForQy($id,$size)
    {
        $data=DB::table($this->tbName)
            ->where('qyid',$id)
            ->select('id','sxm','sxb','dcsrq','sxrzw','drzrq','szjlx','szjhm','szzmm','szc')
            ->paginate($size);
        return $data;
    }

    public function deleteGlry($id)
    {
        $this->delete($id);
    }

    public function showByQy($id)
    {
        $data=DB::table($this->tbName)->where('id',$id)->first();
        return $data;
    }

    public function getGlryByQyId($qyid)
    {
        $res = DB::table($this->tbName)
            ->where('qyid',$qyid)
            ->select('id','sxm','sxb','dcsrq','sxrzw','drzrq','szjlx','szjhm','szzmm','szc')
            ->get();
        return $res;
    }
}