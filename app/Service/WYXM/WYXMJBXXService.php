<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/21
 * Time: 下午1:49
 */

namespace App\Service\WYXM;


use App\Service\BaseService;
use App\Tools\DtcxTool;
use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class WYXMJBXXService extends BaseService
{
    protected $tbName = 't_xm_jbxx';

    /**
     * 获取企业服务的所有项目
     * @param $qyId
     * @return mixed
     */
    public function getXmsByQyId($qyId)
    {
        return $this->show([
            ['sqyid', '=', $qyId],
        ]);
    }

    /**
     * 根据项目id获取项目信息
     * @param $XmId
     * @return mixed
     */
    public function getXmByXmId($XmId)
    {
        $data=DB::table($this->tbName)->where('t_xm_jbxx.id',$XmId)->leftJoin('t_qyjbxx','t_qyjbxx.id','=','t_xm_jbxx.sqyid')
              ->select('t_xm_jbxx.*','t_qyjbxx.sqymc')
              ->first();
        return $data;

    }


    public function search($sxmmc, $sxmbh, $sxzqh, $szldd, $skfdw, $sxmlx, $swyqy, $xmzt, $xzqh = '2*130000000',$status, $size, $select = ['*'])
    {
        $xzqh = $xzqh ?? '2*130000';
        $xzqh = DtcxTool::getXzqhTrueStr($xzqh);
        if( $sxzqh != null)
            $sxzqh = SqlTool::getCodeBmBymc($sxzqh);
        $where = [];
        array_push($where, ['sssqx', 'like', $xzqh . '%']);


        if ($sxmmc !== null && $sxmmc !== '') {
            array_push($where, ['sxmmc', 'like', '%' . $sxmmc . '%']);
        }

        if ($sxmbh !== null && $sxmbh !== '') {
            array_push($where, ['sxmbh', '=', $sxmbh]);
        }

        if ($sxzqh !== null && $sxzqh !== '') {
            array_push($where, ['sssqx', '=', $sxzqh]);
        }

        if ($sxmlx !== null && $sxmlx !== '') {
            array_push($where, ['sxmlx', '=', $sxmlx]);
        }

        if ($szldd !== null && $szldd !== '') {
            array_push($where, ['szldd', 'like', '%' . $szldd . '%']);
        }
        if ($skfdw !== null && $skfdw !== '') {
            array_push($where, ['skfjsdw', 'like', '%' . $skfdw . '%']);
        }
        if ($swyqy !== null && $swyqy !== '') {
            array_push($where, ['sqymc', 'like', '%'.$swyqy.'%']);
        }
        if ($xmzt !== null && $xmzt !== '') {
            array_push($where, ['sxmzt', '=', $xmzt]);
        }
        if ($status !== null && $status !== '') {
            array_push($where, ['t_xm_jbxx.sstatus', '=', $status]);
        }
        $data = DB::table($this->tbName)
            ->leftjoin('t_qyjbxx', 't_qyjbxx.id', '=', $this->tbName . '.sqyid')
            ->where($where)
            ->leftjoin('t_xzqh', 't_xzqh.bm','=',$this->tbName.'.sssqx')
            ->select($select)
            ->paginate($size);
        return $data;
    }

    public function getQyByXmId($xmId)
    {
        $qyxx = DB::table('t_qyjbxx')->join($this->tbName, $this->tbName . '.' . 'sqyid', '=', 't_qyjbxx.id')
            ->where($this->tbName . '.' . 'id', '=', $xmId)
            ->select('t_qyjbxx.*')
            ->first();
        return $qyxx;
    }

    public function fixXmxx($xmid,$data){
        DB::table($this->tbName)
            ->where('id',$xmid)
            ->update($data);
    }

    public function getXmXgxx($xmid)
    {
        $xmjbxx = $this->getXmByXmId($xmid);


//        $yrzxx = DB::table('t_xm_yrzxx')->where('xmid',$xmid)
//            ->select('xmid',DB::raw('COUNT(xmid) as yrzqs, SUM(nrzgm) as rzgm ,SUM(ifwzs) as fwzs , SUM(ifwts) as rzts'))
//            ->groupBy('xmid')
//            ->first();
        // 已入住信息

        $yrzxx= DB::table('t_xm_yrzxx')->where('xmid',$xmid)
            ->orderBy('irzqs','asc')
            ->get()->toArray();
        $rypzqk = DB::table('t_xm_rypz')->where('xmid', $xmid)->first();
        $yzqk = DB::table('t_xm_yzxx')->where('xmid', $xmid)->first();
        $sfqk = DB::table('t_xm_sfbz')->where('sxmid', $xmid)->get()->toArray();
        $data = [
            'xmjbxx' => $xmjbxx,
            'yrzxx' => $yrzxx,
            'rypzqk' => $rypzqk,
            'yzqk' => $yzqk,
            'sfqk'=> $sfqk
        ];
        return $data;
    }

    public function initXm($xmxx,$ywhxx,$rypz,$yzxx){
        DB::transaction(function ()use ($xmxx,$ywhxx,$rypz,$yzxx){
            DB::table($this->tbName)->insert($xmxx);
            DB::table('t_ywh_jbxx')->insert($ywhxx);
            DB::table('t_xm_rypz')->insert($rypz);
            DB::table('t_xm_yzxx')->insert($yzxx);
//            DB::table('t_xm_sfbz')->insert($sfbz);
        });

    }

    public function deleteXm($xmid){
        DB::transaction(function ()use ($xmid){
            DB::table($this->tbName)->where('id',$xmid)->delete();
            DB::table('t_ywh_jbxx')->where('xmid',$xmid)->delete();
            DB::table('t_xm_rypz')->where('xmid',$xmid)->delete();
            DB::table('t_xm_yzxx')->where('xmid',$xmid)->delete();
            DB::table('t_xm_sfbz')->where('sxmid',$xmid)->delete();
        });
    }

    public function getQyMc($xmid)
    {
        $qyid=DB::table($this->tbName)->where('id',$xmid)->value('sqyid');
        $qymc=DB::table('t_qyjbxx')->where('id',$qyid)->value('sqymc');
        return $qymc;
    }

    public function resetPassword($xmid){
        DB::table($this->tbName)
            ->where('id',$xmid)
            ->update([
                'sdlmm'=>md5('123456')
            ]);
    }

    public function getbz($id)
    {
        $tbz=DB::table($this->tbName)->where('id',$id)->value('tbz');
        return $tbz;
    }
}