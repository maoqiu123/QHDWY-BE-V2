<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/21
 * Time: 下午1:49
 */

namespace App\Service\WYXM;


use App\Service\BaseService;
use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class XMJLService extends BaseService
{
    protected $tbName = 't_xm_rypz_xmjl';

    /**
     * 搜索项目经理
     */
    public function search($sxmbh, $sxm, $ssfz, $size)
    {
        $where = [];
        if ($sxmbh !== null && $sxmbh !== '') {
            array_push($where, ['sxmbh', '=', $sxmbh]);
        }

        if ($sxm !== null && $sxm !== 0) {
            array_push($where, ['irzqs', 'like', '%' . $sxm . '%']);
        }

        if ($ssfz !== null && $ssfz !== '') {
            array_push($where, ['sghlx', '=', $ssfz]);
        }

        $data = DB::table($this->tbName)
            ->where($where)
            ->paginate($size);
        return $data;
    }

    /**
     * 房管局端专用搜索
     * @param $xmmc ;项目名称
     * @param $xm  ;姓名
     * @param $sfzh; 身份证号
     * @param $xl ;学历
     * @param $sfyz ;是否有证
     * @param $xmjlzsh ;项目经理证书号
     * @param $size; 页数
     * @return mixed
     */
    public function searchForFgj($xmmc,$xm,$sfzh,$xl,$sfyz,$xmjlzsh,$size){
        $where = [];
        if ($xmmc !== null && $xmmc !== '') {
            array_push($where, ['sxmmc', 'like', $xmmc.'%']);
        }
        if ($xm !== null && $xm !== '') {
            array_push($where, ['sxm', 'like', '%'.$xm.'%']);
        }
        if ($sfzh !== null && $sfzh !== '') {
            array_push($where, ['ssfz', 'like', '%'.$sfzh.'%']);
        }
        if ($xl !== null && $xl !== '') {
            array_push($where, ['swhcd', '=', $xl]);
        }
        if ($sfyz !== null && $sfyz !== '') {
            array_push($where, ['ssfyz', '=', $sfyz]);
        }
        if ($xmjlzsh !== null && $xmjlzsh !== '') {
            array_push($where, ['sxmjlzsh', 'like', '%'.$xmjlzsh.'%']);
        }
        $xmjls=DB::table($this->tbName)
            ->join('t_xm_jbxx',$this->tbName.'.'.'xmid','=','t_xm_jbxx.id')
            ->where($where)
            ->select('t_xm_jbxx.sxmmc','t_xm_rypz_xmjl.*')
            ->paginate($size);
        return $xmjls;
    }

    public function getXmjlxxxx($jlid){
        $xmjl = DB::table($this->tbName)->where('t_xm_rypz_xmjl.id',$jlid)
            ->join('t_xm_jbxx','t_xm_rypz_xmjl.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_jbxx.sxmmc','t_xm_rypz_xmjl.*')
            ->first();
        return $xmjl;
    }

    public function getXmbh($id){
        $xmbh=DB::table('t_xm_jbxx')->where('id',$id)->value('sxmbh');
        return $xmbh;
    }

    public function searchForXm($name,$xmid,$size){
        $where=[];
        if($name!==null&&$name!=='')
            array_push($where,['sxm','like','%'.$name.'%']);
        if($xmid!==null&&$xmid!=='')
            array_push($where,['xmid','=',$xmid]);
        $data=DB::table($this->tbName)
            ->where($where)
            ->select('id','sxm','ssfz','sxb','dcsrq','swhcd','szzmm','slxdh','ssj')
            ->paginate($size);
        foreach ($data as $item){
            $item->dcsrq = SqlTool::getNYRTime($item->dcsrq);
        }
        return $data;
    }
}