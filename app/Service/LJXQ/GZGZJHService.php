<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午9:31
 */

namespace App\Service\LJXQ;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class GZGZJHService extends BaseService
{
    protected $tbName = 't_ljxq_gzgzjh';
    /**
     * 不同于老旧小区的基本信息查询
     * @param $community_name
     * @param $address
     * @param $transformation_state
     * @param $effective_sign
     * @param int $size
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchLjxqjbxx($community_name,$transformation_state,$effective_sign,int $size)
    {
        $where=[];
        if ($community_name !== null) {
            array_push($where, ['t_ljxq_jbxx.sxqmc', 'like', '%' . $community_name . '%']);
        }
//        if ($address !== null) {
//            array_push($where, ['t_ljxq_jbxx.sdz', 'like', '%' . $address . '%']);
//        }

        if ($transformation_state!==null){
            array_push($where,['t_ljxq_jbxx.sgzzt','=',$transformation_state]);
        }
        if ($effective_sign!==null){
            array_push($where,['t_ljxq_jbxx.syxbz','=',$effective_sign]);
        }

        $data = DB::table('t_ljxq_jbxx')
            ->select('id','sxqmc','sgznr', 'sgzzt','dqdrq','dwcrq','ssgdw')
            ->where($where)
            ->paginate($size);
        return $data;
    }
    public function searchGzgzjh($ljxq_id,int $size){
        $data = DB::table($this->tbName)
            ->select('id','dksrq','djzrq', 'sgznr','syxbz','sbz')
            ->where('sxqid',$ljxq_id)
            ->paginate($size);
        foreach ($data as $datum){
            $count = DB::table('t_ljxq_gzgzjd_jzqk')
                ->where('sgzjhid',$datum->id)
                ->count();
            $datum->ssbjls = $count;
        }
        return $data;
    }

    public function deleteGzjh($id)
    {
        $res = DB::table('t_ljxq_gzgzjd_jzqk')->where('sgzjhid',$id)->count();
        if($res > 0)
            return false;
        $this->delete($id);
        return true;
    }

}