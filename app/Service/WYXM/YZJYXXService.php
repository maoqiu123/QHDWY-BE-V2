<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/7/19
 * Time: 下午2:52
 */

namespace App\Service\WYXM;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class YZJYXXService extends BaseService
{
    protected $tbName = 't_yz_yzjyxx';

    public function searchForYwh($ywhId,$startdate,$enddate,$status,$assess,$size){
        $where=[];
        if (!empty($startdate)){
            array_push($where,['t_yz_yzjyxx.djyrq','>',$startdate]);
        }
        if (!empty($enddate)){
            array_push($where,['t_yz_yzjyxx.djyrq','<',$enddate]);
        }
        if (!empty($status)){
            array_push($where,['t_yz_yzjyxx.sslzt','=',$status]);
        }
        if (!empty($assess)) {
            array_push($where, ['t_yz_yzjyxx.syzpj', '=', $assess]);
        }
        $data = DB::table($this->tbName)
            ->join('t_yz_yzjbxx','t_yz_yzjyxx.yzid','=','t_yz_yzjbxx.id')
            ->join('t_qyjbxx','t_qyjbxx.id','=','t_yz_yzjyxx.qyid')
            ->join('t_xm_jbxx','t_xm_jbxx.id','=','t_yz_yzjbxx.xmid')
            ->join('t_ywh_jbxx','t_ywh_jbxx.xmid','=','t_xm_jbxx.id')
            ->where('t_ywh_jbxx.id','=',$ywhId)
            ->where($where)
            ->select('t_yz_yzjyxx.id', 't_yz_yzjyxx.djyrq','t_qyjbxx.sqymc', 't_yz_yzjyxx.sjysx', 't_yz_yzjyxx.sjynr', 't_yz_yzjyxx.sslzt', 't_yz_yzjyxx.dslrq', 't_yz_yzjyxx.sblqk', 't_yz_yzjyxx.dbjrq', 't_yz_yzjyxx.syzpj', 't_yz_yzjyxx.sbmynr')
            ->orderBy('t_yz_yzjyxx.djyrq','desc')
            ->paginate($size);
        return $data;
    }

    public function getSuggestionInfoBy($id)
    {
        $ownerInfo =  DB::table($this->tbName)
            ->where('t_yz_yzjyxx.id',$id)
            ->join('t_qyjbxx','t_qyjbxx.id','=','t_yz_yzjyxx.qyid')
            ->select('djyrq','slxdh','t_qyjbxx.sqymc','sjysx','sjynr','sslzt','dslrq','sblqk','dbjrq','dhfrq','syzpj','dpjrq','sbmynr')
            ->first();
        return $ownerInfo;
    }
}