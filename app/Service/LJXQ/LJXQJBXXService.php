<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 18/6/22
 * Time: 上午3:39
 */

namespace App\Service\LJXQ;


use App\Service\BaseService;
use App\Tools\DtcxTool;
use Illuminate\Support\Facades\DB;

class LJXQJBXXService extends BaseService
{
    protected $tbName = 't_ljxq_jbxx';

    public function getLjxqById($ljxqId){
        $ljxq=DB::table($this->tbName)->where('id','=',$ljxqId)->first();
        return $ljxq;
    }
    public function getJWDmh($name,$xzqh){
        $where=[];
        if ($name != ''&& $name!==null){
            array_push($where,['sxqmc','like','%'.$name.'%']);
        }
        $xzqh = DtcxTool::getXzqhTrueStr($xzqh);
        array_push($where,['sssqx','like',$xzqh.'%']);
        $jwd = DB::table($this->tbName)->where($where)->get();
        return $jwd;
    }

    public function getJWD($xzqh){
        $jwds=DB::table($this->tbName)->where([
            ['sssqx','like',$xzqh.'%'],
            ['njd','<>',null],
            ['nwd','<>',null]
        ])->select('id','sxqmc','njd','nwd')->get();
        return $jwds;
    }

    public function getGzgzjhById($ljxqId){
        $gzjh = DB::table('t_ljxq_gzgzjh')->where('sxqid','=',$ljxqId)->get();
        return $gzjh;
    }

    public function getGzgzjdById($gzjhId){
        $gzjd = DB::table('t_ljxq_gzgzjd_jzqk')->where('sgzjhid','=',$gzjhId)->first();
        return $gzjd;
    }
    public function searchLjxqjbxx($community_name,$address,$transformation_state,$effective_sign,int $size,$xzqh,$sssqx)
    {
        $where=[];

        $xzqhType = substr($xzqh,0,6);
        if($xzqhType !== '130300')
        {
            $where[] = [
                'sssqx', 'like', $xzqhType . '%'
            ];
        }
        if ($sssqx !== null) {
            $where[] = [
                'sssqx', 'like', substr($sssqx,0,6) . '%'
            ];
        }
        if ($community_name !== null) {
            array_push($where, ['t_ljxq_jbxx.sxqmc', 'like', '%' . $community_name . '%']);
        }
        if ($address !== null) {
            array_push($where, ['t_ljxq_jbxx.sdz', 'like', '%' . $address . '%']);
        }

        if ($transformation_state!==null){
            array_push($where,['t_ljxq_jbxx.sgzzt','=',$transformation_state]);
        }
        if ($effective_sign!==null){
            array_push($where,['t_ljxq_jbxx.syxbz','=',$effective_sign]);
        }

        $data = DB::table($this->tbName)
            ->where($where)
            ->paginate($size);
        foreach ($data as $dat){
            $ssqx = DtcxTool::getChineseXzqh($dat->sssqx);
            $dat->sssqx = $ssqx;
        }
        return $data;
    }

    public function deleteJbxx($id)
    {
        $res = DB::table('t_ljxq_gzgzjh')->where('sxqid',$id)->count();
        if($res > 0)
            return false;
        $this->delete($id);
        return true;
    }
}