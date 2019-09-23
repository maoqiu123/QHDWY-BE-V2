<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/11/11
 * Time: 21:26
 */

namespace App\Service\LJXQ;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class GZGZJHCBDWSBNRService extends BaseService
{
    protected $tbName = 't_ljxq_gzjh_cbdw_sbnr';

    public function checkDelete($id){
        if (DB::table($this->tbName)->where('id',$id)->value('sstatus') == '提交'){
            return -1;
        }else{
            return 0;
        }
    }
    /**
     * 检测权限
     */
    public function checkPower($ssbid,$xzqh){
        $condition = [];
        array_push($condition,['t_ljxq_gzjh_cbdw.srwid','=',$ssbid]);
        array_push($condition,['t_ljxq_gzjh_cbdw.scbdwid','=',$xzqh]);
        $data = DB::table('t_ljxq_gzjh_cbdw')->where($condition)->first();
        if ($data == null){
            return -1;
        }else{
            return 0;
        }
    }
    public function submmit($id){
        if (DB::table($this->tbName)->where('id',$id)->value('sstatus') == '提交'){
            return -1;
        }
        DB::table($this->tbName)->where('id',$id)->update(['sstatus'=>'提交']);
        return 0;
    }
    public function search($rwid,$request){
        $condition = [];
        if ($request->number == null){
            $num = 10;
        }
        array_push($condition,['t_ljxq_gzjh_cbdw_sbnr.ssbid','=',$rwid]);
        return $this->showWithPaginate($condition,$num);
    }
    public function getById($id){
        return DB::table($this->tbName)->where('id',$id)->first();
    }
}