<?php
namespace App\Service\GKGS;

use App\Service\BaseService;
use Carbon\Carbon;
use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class HTLXQKService extends BaseService
{
    protected $tbName='t_xm_gs_htlxqk_zb';

    /**
     * 根据项目名称和企业名称进行模糊匹配，并筛选出在公示日期区间内的合同履行情况公示
     * @param $entry_name
     * @param $enterpriseName
     * @param $publicity_begin
     * @param $publicity_end
     * @param int $size
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchHtlxqk($entry_name , $enterpriseName, $publicity_begin, $publicity_end, int $size)
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
            array_push($where,['t_xm_gs_htlxqk_zb.dgsrq','>=',$publicity_begin]);
        }
        if ($publicity_end!==null){
            array_push($where,['t_xm_gs_htlxqk_zb.dgsrq','<=',$publicity_end]);
        }

        $datas = DB::table($this->tbName)
            ->join('t_xm_ba_ht','t_xm_gs_htlxqk_zb.htid','=','t_xm_ba_ht.id')
            ->join('t_qyjbxx','t_xm_ba_ht.qyid','=','t_qyjbxx.id')
            ->join('t_xm_jbxx','t_xm_ba_ht.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_gs_htlxqk_zb.id','t_xm_jbxx.sxmmc', 't_qyjbxx.sqymc','t_xm_ba_ht.shtmc','t_xm_gs_htlxqk_zb.tgsbt','t_xm_gs_htlxqk_zb.sgsnr','t_xm_gs_htlxqk_zb.dzq_q','t_xm_gs_htlxqk_zb.dzq_z','t_xm_gs_htlxqk_zb.dgsrq','t_xm_gs_htlxqk_zb.ffj')
            ->where($where)
            ->orderBy('t_xm_gs_htlxqk_zb.dgsrq','desc')
            ->paginate($size);
        foreach ($datas as $data) {
            $data->ffj = json_decode($data->ffj);
        }
        return $datas;
    }


     public function searchByQyId($qyid,$month,$size){
        $time = new Carbon();
        $checkTime = $time->subMonth($month);
        $data= DB::table($this->tbName)
            ->join('t_xm_ba_ht','t_xm_gs_htlxqk_zb.htid','=','t_xm_ba_ht.id')
            ->join('t_qyjbxx','t_xm_ba_ht.qyid','=','t_qyjbxx.id')
            ->join('t_xm_jbxx','t_xm_ba_ht.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_gs_htlxqk_zb.*','t_xm_jbxx.sxmmc', 't_qyjbxx.sqymc')
            ->where([
                ['t_qyjbxx.id','=',$qyid],
                ['t_xm_gs_htlxqk_zb.dgsrq','>',$checkTime]
            ])
            ->orderBy('t_xm_gs_htlxqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function searchByXmId($xmid,$month,$size){
        $time = new Carbon();
        $checkTime = $time->subMonth($month);
        $data= DB::table($this->tbName)
            ->join('t_xm_ba_ht','t_xm_gs_htlxqk_zb.htid','=','t_xm_ba_ht.id')
            ->join('t_qyjbxx','t_xm_ba_ht.qyid','=','t_qyjbxx.id')
            ->join('t_xm_jbxx','t_xm_ba_ht.xmid','=','t_xm_jbxx.id')
            ->select('t_xm_gs_htlxqk_zb.*','t_xm_jbxx.sxmmc', 't_qyjbxx.sqymc')
            ->where([
                ['t_xm_jbxx.id','=',$xmid],
                ['t_xm_gs_htlxqk_zb.dgsrq','>',$checkTime]
            ])
            ->orderBy('t_xm_gs_htlxqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }



    public function getstatus($id)
    {
        $status=DB::table($this->tbName)
            ->where('id',$id)->select('sstatus')->value('sstatus');
        return $status;
    }

    public function saveFiles($path,$name)
    {
        $data = [
            'id' => SqlTool::makeUUID(),
            'fjlj' => $path,
            'fjmc' => $name
        ];
        $this->create($data);
        return $data['id'];
    }

    public function searchHtlxqkForWy($id,$publicity_begin, $publicity_end, int $size)
    {

        $where = [];
        if ($id !== null) {
            array_push($where, ['t_xm_ba_ht.xmid', '=', $id]);
        }
        if ($publicity_begin !== null) {
            array_push($where, ['t_xm_gs_htlxqk_zb.dgsrq', '>=', $publicity_begin]);
        }
        if ($publicity_end !== null) {
            array_push($where, ['t_xm_gs_htlxqk_zb.dgsrq', '<=', $publicity_end]);
        }

        $data = DB::table($this->tbName)
            ->join('t_xm_ba_ht', 't_xm_gs_htlxqk_zb.htid', '=', 't_xm_ba_ht.id')
            ->select('t_xm_gs_htlxqk_zb.id', 't_xm_ba_ht.shtmc', 't_xm_gs_htlxqk_zb.tgsbt', 't_xm_gs_htlxqk_zb.sgsnr', 't_xm_gs_htlxqk_zb.dzq_q', 't_xm_gs_htlxqk_zb.dzq_z', 't_xm_gs_htlxqk_zb.dgsrq', 't_xm_gs_htlxqk_zb.ffj', 't_xm_gs_htlxqk_zb.sstatus')
            ->where($where)
            ->orderBy('t_xm_gs_htlxqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function searchHtForWy($id,$publicity_begin, $publicity_end, int $size)
    {

        $where = [];
        if ($id !== null) {
            array_push($where, ['xmid', '=', $id]);
        }
        if ($publicity_begin !== null) {
            array_push($where, ['dhtjssj', '>=', $publicity_begin]);
        }
        if ($publicity_end !== null) {
            array_push($where, ['dhtkssj', '<=', $publicity_end]);
        }

        $data = DB::table('t_xm_ba_ht')
            ->select('id', 'shtmc',  'dhtkssj', 'dhtjssj')
            ->where($where)
            ->paginate($size);
        return $data;
    }
    public function searchHtlxqkForYz($id,$publicity_begin, $publicity_end, int $size)
    {

        $where = [];
        if ($id !== null) {
            array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        }
        if ($publicity_begin !== null) {
            array_push($where, ['t_xm_gs_htlxqk_zb.dgsrq', '>=', $publicity_begin]);
        }
        if ($publicity_end !== null) {
            array_push($where, ['t_xm_gs_htlxqk_zb.dgsrq', '<=', $publicity_end]);
        }
        array_push($where, ['t_xm_gs_htlxqk_zb.sstatus', '=', '提交']);
        $data = DB::table('t_yz_yzjbxx')
            ->Join('t_xm_ba_ht', 't_yz_yzjbxx.xmid', '=', 't_xm_ba_ht.xmid')
            ->Join('t_xm_gs_htlxqk_zb', 't_xm_ba_ht.id', '=', 't_xm_gs_htlxqk_zb.htid')
            ->select('t_xm_gs_htlxqk_zb.id', 't_xm_ba_ht.shtmc', 't_xm_gs_htlxqk_zb.tgsbt', 't_xm_gs_htlxqk_zb.sgsnr', 't_xm_gs_htlxqk_zb.dzq_q', 't_xm_gs_htlxqk_zb.dzq_z', 't_xm_gs_htlxqk_zb.dgsrq', 't_xm_gs_htlxqk_zb.ffj', 't_xm_gs_htlxqk_zb.sstatus')
            ->where($where)
            ->orderBy('t_xm_gs_htlxqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }
    public function searchHtlxqkForYwh($id,$publicity_begin, $publicity_end, int $size)
    {

        $where = [];
        if ($id !== null) {
            array_push($where, ['t_ywh_jbxx.id', '=', $id]);
        }
        if ($publicity_begin !== null) {
            array_push($where, ['t_xm_gs_htlxqk_zb.dgsrq', '>=', $publicity_begin]);
        }
        if ($publicity_end !== null) {
            array_push($where, ['t_xm_gs_htlxqk_zb.dgsrq', '<=', $publicity_end]);
        }
        array_push($where, ['t_xm_gs_htlxqk_zb.sstatus', '=', '提交']);
        $data = DB::table('t_ywh_jbxx')
            ->Join('t_xm_ba_ht', 't_ywh_jbxx.xmid', '=', 't_xm_ba_ht.xmid')
            ->Join('t_xm_gs_htlxqk_zb', 't_xm_ba_ht.id', '=', 't_xm_gs_htlxqk_zb.htid')
            ->select('t_xm_gs_htlxqk_zb.id', 't_xm_ba_ht.shtmc', 't_xm_gs_htlxqk_zb.tgsbt', 't_xm_gs_htlxqk_zb.sgsnr', 't_xm_gs_htlxqk_zb.dzq_q', 't_xm_gs_htlxqk_zb.dzq_z', 't_xm_gs_htlxqk_zb.dgsrq', 't_xm_gs_htlxqk_zb.ffj', 't_xm_gs_htlxqk_zb.sstatus')
            ->where($where)
            ->orderBy('t_xm_gs_htlxqk_zb.dgsrq','desc')
            ->paginate($size);
        return $data;
    }

    public function showDetail($id,$hlxqkId,$data){
        $where = [];
        array_push($where, ['t_yz_yzjbxx.id', '=', $id]);
        array_push($where, ['t_xm_gs_htlxqk_zb.id', '=', $hlxqkId]);
        $change = [
            't_xm_ba_ht.shtmc' => $data['shtmc'],
            't_xm_gs_htlxqk_zb.tgsbt' => $data['tgsbt'],
            't_xm_gs_htlxqk_zb.dzq_q' => $data['dzq_q'],
            't_xm_gs_htlxqk_zb.dzq_z' => $data['dzq_z'],
            't_xm_gs_htlxqk_zb.dgsrq' => $data['dgsrq'],
            't_xm_gs_htlxqk_zb.sgsnr' => $data['sgsnr'],
            't_xm_gs_htlxqk_zb.sstatus' => '保存'
         ];
        $data = DB::table('t_yz_yzjbxx')
            ->Join('t_xm_ba_ht', 't_yz_yzjbxx.xmid', '=', 't_xm_ba_ht.xmid')
            ->Join('t_xm_gs_htlxqk_zb', 't_xm_ba_ht.id', '=', 't_xm_gs_htlxqk_zb.htid')
            ->where($where)
            ->update($change);
        if ($data){
            return 1000;
        }else{
            return 1001;
        }
    }

    public function getHtListByXmId($xmid){
        $list=DB::table('t_xm_ba_ht')->where('xmid',$xmid)->select('id','shtmc')->get();
        return $list;
    }


    public function getQyHtlxqkNum($qyid){
        $num = DB::table('t_xm_ba_ht')
            ->where('qyid',$qyid)
            ->join($this->tbName,'t_xm_ba_ht.id','=',$this->tbName.'.'.'htid')
            ->count();
        return $num;
    }
    public function getXmHtlxqkNum($xmid){
        $num = DB::table('t_xm_ba_ht')
            ->where('xmid',$xmid)
            ->join($this->tbName,'t_xm_ba_ht.id','=',$this->tbName.'.'.'htid')
            ->count();
        return $num;
    }
}