<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/11/15
 * Time: 19:51
 */

namespace App\Service\LJXQ;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class LJXQTZGGService extends BaseService
{
    protected $tbName = 't_ljxq_tzgg';
    public function submmit($id){
        if (DB::table($this->tbName)->where('id',$id)->value('sstatus') == '提交'){
            return -1;
        }
        DB::table($this->tbName)->where('id',$id)->update(['sstatus'=>'提交']);
        return 0;
    }
    public function checkDelete($id){
        if (DB::table($this->tbName)->where('id',$id)->value('sstatus') == '提交'){
            return -1;
        }else{
            return 0;
        }
    }
    public function search($request){
        $condition = [];
        if ($request->number == null){
            $num = 10;
        }
        if (!$request->sbt == null){
            array_push($condition,['t_ljxq_tzgg.sbt','like','%'.$request->srwbt.'%']);
        }
        if (!$request->dstart == null){
            array_push($condition,['t_ljxq_tzgg.dfbrq','>=',$request->dstart]);
        }
        if (!$request->dend == null){
            array_push($condition,['t_ljxq_tzgg.dfbrq','<=',$request->dend]);
        }
        if (!$request->sstatus == null){
            array_push($condition,['t_ljxq_tzgg.sstatus','=',$request->sstatus]);
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
}