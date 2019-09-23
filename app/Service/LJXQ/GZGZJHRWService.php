<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/11/8
 * Time: 19:47
 */

namespace App\Service\LJXQ;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class GZGZJHRWService extends BaseService
{
    protected $tbName = 't_ljxq_sbrw';

    public function search($request){
        $condition = [];
        if ($request->number == null){
            $num = 10;
        }
        if (!$request->srwbt == null){
            array_push($condition,['t_ljxq_sbrw.srwbt','like','%'.$request->srwbt.'%']);
        }
        if (!$request->dstart == null){
            array_push($condition,['t_ljxq_sbrw.dfbrq','>=',$request->dstart]);
        }
        if (!$request->dend == null){
            array_push($condition,['t_ljxq_sbrw.dfbrq','<=',$request->dend]);
        }
        if (!$request->sbjbz == null){
            array_push($condition,['t_ljxq_sbrw.sbjbz','=',$request->sbjbz]);
        }
        if (!$request->sstatus == null){
            array_push($condition,['t_ljxq_sbrw.sstatus','=',$request->sstatus]);
        }
        $datas = $this->showWithPaginate($condition,$num);
        foreach ($datas as $data){
            $count = DB::table('t_ljxq_gzjh_cbdw')->where('srwid',$data->id)->count();
            $data->ndwsl = $count;
        }
        return $datas;
    }
    public function getById($id){
        return DB::table($this->tbName)->where('id',$id)->first();
    }
    public function submmit($id){
        if (DB::table($this->tbName)->where('id',$id)->value('sstatus') == '提交'){
            return -1;
        }
        if (DB::table($this->tbName)->where('id',$id)->value('sstatus') == '撤回'){
            return -2;
        }
        DB::table($this->tbName)->where('id',$id)->update(['sstatus'=>'提交']);
        return 0;
    }
    public function callback($id){
        if (DB::table($this->tbName)->where('id',$id)->value('sstatus') == '撤回'){
            return -1;
        }
        DB::table($this->tbName)->where('id',$id)->update(['sstatus'=>'撤回']);
        return 0;
    }
    public function finish($id){
        if (DB::table($this->tbName)->where('id',$id)->value('sbjbz') == '办结'){
            return -1;
        }
        DB::table($this->tbName)->where('id',$id)->update(['sbjbz'=>'办结']);
        return 0;
    }
    public function checkDelete($id){
        if (DB::table($this->tbName)->where('id',$id)->value('sstatus') == '提交'){
            return -1;
        }elseif (json_decode(DB::table('t_ljxq_gzjh_cbdw_sbnr')->where('ssbid',$id)->get()) != null){
            return -2;
        }else{
            return 0;
        }
    }
    public function deleteCbdw($id){
        DB::table('t_ljxq_gzjh_cbdw')->where('srwid',$id)->delete();
    }
    public function isSelectExist($srwid,$scbdwid){
        $where = [];
        array_push($where,['t_ljxq_gzjh_cbdw.srwid','=',$srwid]);
        array_push($where,['t_ljxq_gzjh_cbdw.scbdwid','=',$scbdwid]);
        $data = DB::table('t_ljxq_gzjh_cbdw')->where($where)->first();
        if ($data == null){
            return 0;
        }else{
            return -1;
        }
    }
}