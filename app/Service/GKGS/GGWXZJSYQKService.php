<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午6:08
 */

namespace App\Service\GKGS;

use App\Service\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GGWXZJSYQKService extends BaseService
{
    protected $tbName='t_xm_gs_ggwxzjsyqk';

    public function searchGgwxzjsyqk($entry_name , $enterpriseName, $publicity_begin, $publicity_end, int $size)
    {
        $where=[];
        $where[] = [
            $this->tbName.'.sstatus', '=', '提交'
        ];
        if ($entry_name !== null) {
            array_push($where, ['t_xm_jbxx.sxmmc', 'like', '%' . $entry_name . '%']);
        }
        if ($enterpriseName !== null) {
            array_push($where, ['t_qyjbxx.sqymc', 'like', '%' . $enterpriseName . '%']);
        }

        if ($publicity_begin!==null){
            array_push($where,['t_xm_gs_ggwxzjsyqk.dgsrq','>=',$publicity_begin]);
        }
        if ($publicity_end!==null){
            array_push($where,['t_xm_gs_ggwxzjsyqk.dgsrq','<=',$publicity_end]);
        }

        $data = DB::table($this->tbName)
            ->join('t_qyjbxx','t_xm_gs_ggwxzjsyqk.qyid','=','t_qyjbxx.id')
            ->join('t_xm_jbxx','t_xm_gs_ggwxzjsyqk.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_gs_ggwxzjsyqk.id','t_xm_jbxx.sxmmc', 't_qyjbxx.sqymc','t_xm_gs_ggwxzjsyqk.dgsrq','t_xm_gs_ggwxzjsyqk.swxxm','t_xm_gs_ggwxzjsyqk.swxxmjs','t_xm_gs_ggwxzjsyqk.nysje','t_xm_gs_ggwxzjsyqk.djhwxrq_q','t_xm_gs_ggwxzjsyqk.djhwxrq_z','t_xm_gs_ggwxzjsyqk.nzyzs','t_xm_gs_ggwxzjsyqk.nzyzsfwmj','t_xm_gs_ggwxzjsyqk.ntyzyzs','t_xm_gs_ggwxzjsyqk.ntyzyzsfwmj')
            ->where($where)
            ->orderBy('t_xm_gs_ggwxzjsyqk.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function searchByQyId($qyid,$month,$size){
        $time = new Carbon();
        $checkTime = $time->subMonth($month);
        $data = DB::table($this->tbName)
            ->join('t_qyjbxx','t_xm_gs_ggwxzjsyqk.qyid','=','t_qyjbxx.id')
            ->join('t_xm_jbxx','t_xm_gs_ggwxzjsyqk.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_gs_ggwxzjsyqk.*','t_xm_jbxx.sxmmc', 't_qyjbxx.sqymc')
            ->where([
                ['t_qyjbxx.id','=',$qyid],
                ['t_xm_gs_ggwxzjsyqk.dgsrq','>',$checkTime]
            ])
            ->orderBy('t_xm_gs_ggwxzjsyqk.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function searchByXmId($xmid,$month,$size){
        $time = new Carbon();
        $checkTime = $time->subMonth($month);
        $data = DB::table($this->tbName)
            ->join('t_qyjbxx','t_xm_gs_ggwxzjsyqk.qyid','=','t_qyjbxx.id')
            ->join('t_xm_jbxx','t_xm_gs_ggwxzjsyqk.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_gs_ggwxzjsyqk.*','t_xm_jbxx.sxmmc', 't_qyjbxx.sqymc')
            ->where([
                ['t_xm_jbxx.id','=',$xmid],
                ['t_xm_gs_ggwxzjsyqk.dgsrq','>',$checkTime]
            ])
            ->orderBy('t_xm_gs_ggwxzjsyqk.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function searchWxzjsyqkForWy($id,$publicity_begin, $publicity_end, int $size)
    {
        $where=[];
        if ($id !== null) {
            array_push($where, ['t_xm_gs_ggwxzjsyqk.xmid', '=', $id]);
        }
        if ($publicity_begin!==null){
            array_push($where,['t_xm_gs_ggwxzjsyqk.dgsrq','>=',$publicity_begin]);
        }
        if ($publicity_end!==null){
            array_push($where,['t_xm_gs_ggwxzjsyqk.dgsrq','<=',$publicity_end]);
        }

        $data = DB::table($this->tbName)
            ->select('id','swxxm','swxxmjs','nysje','djhwxrq_q','djhwxrq_z','nzyzs','nzyzsfwmj','ntyzyzs','ntyzyzsfwmj','dgsrq','sstatus')
            ->where($where)
            ->orderBy('t_xm_gs_ggwxzjsyqk.dgsrq','desc')
            ->paginate($size);
        return $data;
    }

    public function getstatus($id)
    {
        $status=DB::table($this->tbName)
            ->where('id',$id)->select('sstatus')->value('sstatus');
        return $status;
    }
    public function searchWxzjsyqkForYz($id,$publicity_begin, $publicity_end, int $size)
    {
        $where = [];
        if ($id !== null) {
            array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        }
        if ($publicity_begin !== null) {
            array_push($where, ['t_xm_gs_ggwxzjsyqk.dgsrq', '>=', $publicity_begin]);
        }
        if ($publicity_end !== null) {
            array_push($where, ['t_xm_gs_ggwxzjsyqk.dgsrq', '<=', $publicity_end]);
        }
        array_push($where, ['t_xm_gs_ggwxzjsyqk.sstatus', '=', '提交']);

        $data = DB::table('t_yz_yzjbxx')
            ->join('t_xm_gs_ggwxzjsyqk', 't_yz_yzjbxx.xmid', '=', 't_xm_gs_ggwxzjsyqk.xmid')
            ->select('t_xm_gs_ggwxzjsyqk.id', 't_xm_gs_ggwxzjsyqk.swxxm', 't_xm_gs_ggwxzjsyqk.swxxmjs', 't_xm_gs_ggwxzjsyqk.nysje', 't_xm_gs_ggwxzjsyqk.djhwxrq_q', 't_xm_gs_ggwxzjsyqk.djhwxrq_z', 't_xm_gs_ggwxzjsyqk.nzyzs', 't_xm_gs_ggwxzjsyqk.nzyzsfwmj', 't_xm_gs_ggwxzjsyqk.ntyzyzs', 't_xm_gs_ggwxzjsyqk.ntyzyzsfwmj', 't_xm_gs_ggwxzjsyqk.dgsrq')
            ->where($where)
            ->orderBy('t_xm_gs_ggwxzjsyqk.dgsrq','desc')
            ->paginate($size);
        return $data;
    }

    public function showDetail($id,$ggwxzjsyqkId,$data){
        $where = [];
        array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        array_push($where, ['t_xm_gs_ggwxzjsyqk.id', '=', $ggwxzjsyqkId]);
        $change = [
            't_xm_gs_ggwxzjsyqk.dgsrq' => $data['dgsrq'],
            't_xm_gs_ggwxzjsyqk.djhwxrq_q' => $data['djhwxrq_q'],
            't_xm_gs_ggwxzjsyqk.djhwxrq_z' => $data['djhwxrq_z'],
            't_xm_gs_ggwxzjsyqk.swxxm' => $data['swxxm'],
            't_xm_gs_ggwxzjsyqk.sstatus' => '保存',
            't_xm_gs_ggwxzjsyqk.swxxmjs' => $data['swxxmjs'],
            't_xm_gs_ggwxzjsyqk.nysje' => $data['nysje'],
            't_xm_gs_ggwxzjsyqk.nzyzs' => $data['nzyzs'],
            't_xm_gs_ggwxzjsyqk.ntyzyzs' => $data['ntyzyzs'],
            't_xm_gs_ggwxzjsyqk.nzyzsfwmj' => $data['nzyzsfwmj'],
            't_xm_gs_ggwxzjsyqk.ntyzyzsfwmj' => $data['ntyzyzsfwmj'],
        ];
        $data = DB::table('t_yz_yzjbxx')
            ->join('t_xm_gs_ggwxzjsyqk', 't_yz_yzjbxx.xmid', '=', 't_xm_gs_ggwxzjsyqk.xmid')
            ->where($where)
            ->update($change);
        if ($data){
            return 1000;
        }else{
            return 1001;
        }
    }

    public function searchForYwh($id,$publicity_begin, $publicity_end, int $size)
    {
        $where = [];
        if ($id !== null) {
            array_push($where, ['t_ywh_jbxx.id', '=', $id]);
        }
        if ($publicity_begin !== null) {
            array_push($where, ['t_xm_gs_ggwxzjsyqk.dgsrq', '>=', $publicity_begin]);
        }
        if ($publicity_end !== null) {
            array_push($where, ['t_xm_gs_ggwxzjsyqk.dgsrq', '<=', $publicity_end]);
        }
        array_push($where, ['t_xm_gs_ggwxzjsyqk.sstatus', '=', '提交']);

        $data = DB::table('t_ywh_jbxx')
            ->join('t_xm_gs_ggwxzjsyqk', 't_ywh_jbxx.xmid', '=', 't_xm_gs_ggwxzjsyqk.xmid')
            ->select('t_xm_gs_ggwxzjsyqk.id', 't_xm_gs_ggwxzjsyqk.swxxm', 't_xm_gs_ggwxzjsyqk.swxxmjs', 't_xm_gs_ggwxzjsyqk.nysje', 't_xm_gs_ggwxzjsyqk.djhwxrq_q', 't_xm_gs_ggwxzjsyqk.djhwxrq_z', 't_xm_gs_ggwxzjsyqk.nzyzs', 't_xm_gs_ggwxzjsyqk.nzyzsfwmj', 't_xm_gs_ggwxzjsyqk.ntyzyzs', 't_xm_gs_ggwxzjsyqk.ntyzyzsfwmj', 't_xm_gs_ggwxzjsyqk.dgsrq')
            ->where($where)
            ->orderBy('t_xm_gs_ggwxzjsyqk.dgsrq','desc')
            ->paginate($size);
        return $data;
    }

    public function getQyWxjjNum($qyid){
        $num = DB::table($this->tbName)
            ->where('qyid',$qyid)
            ->count();
        return $num;
    }
    public function getXmWxjjNum($xmid){
        $num = DB::table($this->tbName)
            ->where('xmid',$xmid)
            ->count();
        return $num;
    }
    public function getqyid($xmid)
    {
        $qyid=DB::table('t_xm_jbxx')
            ->where('id',$xmid)
            ->value('sqyid');
        return $qyid;
    }
}