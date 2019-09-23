<?php
namespace App\Service\GKGS;

use App\Service\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WTJYSZQKService extends BaseService
{
    protected $tbName='t_xm_gs_wtjyszqk_zb';

    /**
     * 根据项目名称和企业名称进行模糊匹配，并筛选出在公示日期区间内的委托经营收支情况公示
     * @param $entry_name
     * @param $enterpriseName
     * @param $publicity_begin
     * @param $publicity_end
     * @param int $size
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchWtjyszqk($entry_name , $enterpriseName, $publicity_begin, $publicity_end, int $size)
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
            array_push($where,['t_xm_gs_wtjyszqk_zb.dgsrq','>=',$publicity_begin]);
        }
        if ($publicity_end!==null){
            array_push($where,['t_xm_gs_wtjyszqk_zb.dgsrq','<=',$publicity_end]);
        }

        $data = DB::table($this->tbName)
            ->join('t_qyjbxx','t_xm_gs_wtjyszqk_zb.qyid','=','t_qyjbxx.id')
            ->join('t_xm_jbxx','t_xm_gs_wtjyszqk_zb.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_gs_wtjyszqk_zb.id','t_xm_jbxx.sxmmc', 't_qyjbxx.sqymc','t_xm_gs_wtjyszqk_zb.sgsbt','t_xm_gs_wtjyszqk_zb.sgsnr','t_xm_gs_wtjyszqk_zb.dzq_q','t_xm_gs_wtjyszqk_zb.dzq_z','t_xm_gs_wtjyszqk_zb.dgsrq','t_xm_gs_wtjyszqk_zb.sbz','t_xm_gs_wtjyszqk_zb.sstatus','t_xm_gs_wtjyszqk_zb.*')
            ->where($where)
            ->orderBy('t_xm_gs_wtjyszqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }

    public function searchByQyId($xmid,$month,$size){
        $time = new Carbon();
        $checkTime = $time->subMonth($month);
        $data = DB::table($this->tbName)
            ->join('t_qyjbxx','t_xm_gs_wtjyszqk_zb.qyid','=','t_qyjbxx.id')
            ->join('t_xm_jbxx','t_xm_gs_wtjyszqk_zb.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_gs_wtjyszqk_zb.*','t_xm_jbxx.sxmmc', 't_qyjbxx.sqymc')
            ->where([
                ['t_qyjbxx.id','=',$xmid],
                ['t_xm_gs_wtjyszqk_zb.dgsrq','>',$checkTime]
            ])
            ->orderBy('t_xm_gs_wtjyszqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function searchByXmId($xmid,$month,$size){
        $time = new Carbon();
        $checkTime = $time->subMonth($month);
        $data = DB::table($this->tbName)
            ->join('t_qyjbxx','t_xm_gs_wtjyszqk_zb.qyid','=','t_qyjbxx.id')
            ->join('t_xm_jbxx','t_xm_gs_wtjyszqk_zb.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_gs_wtjyszqk_zb.*','t_xm_jbxx.sxmmc', 't_qyjbxx.sqymc')
            ->where([
                ['t_xm_jbxx.id','=',$xmid],
                ['t_xm_gs_wtjyszqk_zb.dgsrq','>',$checkTime]
            ])
            ->orderBy('t_xm_gs_wtjyszqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function searchWtjyszqkForWy($id,$publicity_begin, $publicity_end, int $size)
    {
        $where=[];
        if ($id !== null) {
            array_push($where, ['t_xm_gs_wtjyszqk_zb.xmid', '=', $id]);
        }
        if ($publicity_begin!==null){
            array_push($where,['dgsrq','>=',$publicity_begin]);
        }
        if ($publicity_end!==null){
            array_push($where,['dgsrq','<=',$publicity_end]);
        }

        $data = DB::table($this->tbName)
            ->select('id','sgsbt','sgsnr','dzq_q','dzq_z','dgsrq','ffj','sstatus')
            ->where($where)
            ->orderBy('t_xm_gs_wtjyszqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function getstatus($id)
    {
        $status=DB::table($this->tbName)
            ->where('id',$id)->select('sstatus')->value('sstatus');
        return $status;
    }

    public function searchWtjyszqkForYz($id,$publicity_begin, $publicity_end, int $size)
    {

        $where = [];
        if ($id !== null) {
            array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        }
        if ($publicity_begin !== null) {
            array_push($where, ['t_xm_gs_wtjyszqk_zb.dgsrq', '>=', $publicity_begin]);
        }
        if ($publicity_end !== null) {
            array_push($where, ['t_xm_gs_wtjyszqk_zb.dgsrq', '<=', $publicity_end]);
        }
        array_push($where, ['t_xm_gs_wtjyszqk_zb.sstatus', '=', '提交']);
        $data = DB::table('t_yz_yzjbxx')
            ->join('t_xm_gs_wtjyszqk_zb', 't_yz_yzjbxx.xmid', '=', 't_xm_gs_wtjyszqk_zb.xmid')
            ->select('t_xm_gs_wtjyszqk_zb.id', 't_xm_gs_wtjyszqk_zb.sgsbt', 't_xm_gs_wtjyszqk_zb.sgsnr', 't_xm_gs_wtjyszqk_zb.dzq_q', 't_xm_gs_wtjyszqk_zb.dzq_z', 't_xm_gs_wtjyszqk_zb.dgsrq', 't_xm_gs_wtjyszqk_zb.ffj')
            ->where($where)
            ->orderBy('t_xm_gs_wtjyszqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function searchWtjyszqkForYwh($id,$publicity_begin, $publicity_end, int $size)
    {

        $where = [];
        if ($id !== null) {
            array_push($where, ['t_ywh_jbxx.id', '=', $id]);
        }
        if ($publicity_begin !== null) {
            array_push($where, ['t_xm_gs_wtjyszqk_zb.dgsrq', '>=', $publicity_begin]);
        }
        if ($publicity_end !== null) {
            array_push($where, ['t_xm_gs_wtjyszqk_zb.dgsrq', '<=', $publicity_end]);
        }
        array_push($where, ['t_xm_gs_wtjyszqk_zb.sstatus', '=', '提交']);
        $data = DB::table('t_ywh_jbxx')
            ->join('t_xm_gs_wtjyszqk_zb', 't_ywh_jbxx.xmid', '=', 't_xm_gs_wtjyszqk_zb.xmid')
            ->select('t_xm_gs_wtjyszqk_zb.id', 't_xm_gs_wtjyszqk_zb.sgsbt', 't_xm_gs_wtjyszqk_zb.sgsnr', 't_xm_gs_wtjyszqk_zb.dzq_q', 't_xm_gs_wtjyszqk_zb.dzq_z', 't_xm_gs_wtjyszqk_zb.dgsrq', 't_xm_gs_wtjyszqk_zb.ffj')
            ->where($where)
            ->orderBy('t_xm_gs_wtjyszqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function showDetail($id,$wtjyszqkId,$data){
        $where = [];
        array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        array_push($where, ['t_xm_gs_wtjyszqk_zb.id', '=', $wtjyszqkId]);
        $change = [
            't_xm_gs_wtjyszqk_zb.sgsbt' => $data['sgsbt'],
            't_xm_gs_wtjyszqk_zb.dzq_q' => $data['dzq_q'],
            't_xm_gs_wtjyszqk_zb.dzq_z' => $data['dzq_z'],
            't_xm_gs_wtjyszqk_zb.sgsnr' => $data['sgsnr'],
            't_xm_gs_wtjyszqk_zb.sstatus' => '保存',
            't_xm_gs_wtjyszqk_zb.sbz' => $data['sbz'],
        ];
        $data = DB::table('t_yz_yzjbxx')
            ->join('t_xm_gs_wtjyszqk_zb', 't_yz_yzjbxx.xmid', '=', 't_xm_gs_wtjyszqk_zb.xmid')
            ->where($where)
            ->update($change);
        if ($data){
            return 1000;
        }else{
            return 1001;
        }
    }
    public function getQyWtjyszqkNum($qyid){
        $num = DB::table($this->tbName)
            ->where('qyid','=',$qyid)
            ->count();
        return $num;
    }
    public function getXmWtjyszqkNum($xmid){
        $num = DB::table($this->tbName)
            ->where('xmid','=',$xmid)
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