<?php

namespace  App\Service\WYXM;

use App\Service\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class YWHJBXXService extends BaseService {
    /*
     * 业主及业委会
     */
    protected $tbName = 't_ywh_jbxx';

    /**
     * 条件搜索业委会成员
     * @param $xmmc 项目名称
     * @param $zrxm 主任姓名
     * @param $startdate 成立日期下限限制
     * @param $enddate 成立日期上限限制
     * @param $size 分页尺寸
     * @param array $select 查询内容
     * @return mixed
     */
    public function search($xmmc,$zrxm,$startdate,$enddate,$size,$select=['*']){
        $where=[];
        if (!empty($xmmc)){
            array_push($where,['t_xm_jbxx.sxmmc','like','%'.$xmmc.'%']);
        }
        if (!empty($zrxm)){
            array_push($where,['t_ywh_jbxx.szrxm','like','%'.$zrxm.'%']);
        }
        if (!empty($startdate)){
            array_push($where,['t_ywh_jbxx.dclsj','>',$startdate]);
        }
        if (!empty($enddate)){
            array_push($where,['t_ywh_jbxx.dclsj','<',$enddate]);
        }
        $data = DB::table($this->tbName)
            ->join('t_xm_jbxx','t_xm_jbxx.id','=',$this->tbName.'.xmid')
            ->where($where)
            ->select($select)
            ->paginate($size);
        return $data;
    }

    /**
     * 根据业委会ID初始化密码,如果返回结果为true,则初始化成功
     * @param $ywhid 业委会id
     * @return bool
     */
    public function initPassword($ywhid){

        try{
            DB::table($this->tbName)->where('id','=',10000000001300)
                ->update([
                    'sdlmm'=>'123456'
                ]);
        }catch (Exception $exception){
            return false;
        }
        return true;
    }

    public function getYwhxxxxByYwhId($ywhid){
        // 详细信息（嘻嘻嘻嘻？）
        $xxxx=DB::table($this->tbName)
            ->where('id',$ywhid)
            ->first();

        return $xxxx;
    }

}