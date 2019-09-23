<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/25
 * Time: 下午6:25
 */

namespace App\Service\WYYZ;


use App\Service\BaseService;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class YZBXXXService extends BaseService
{
    protected $tbName = 't_yz_yzbxxx';

    public function searchRepairInfo($xmid,$startTime,$endTime,$status,$size)
    {
        $where=[];
        if (!empty($xmid)){
            array_push($where,['t_yz_yzjbxx.xmid','=',$xmid]);
        }
        if (!empty($startTime)){
            array_push($where,['t_yz_yzbxxx.dbxrq','>',$startTime]);
        }
        if (!empty($endTime)){
            array_push($where,['t_yz_yzbxxx.dbxrq','<',$endTime]);
        }
        if (!empty($status)){
            array_push($where,['t_yz_yzbxxx.sslzt','=',$status]);
        }
        array_push($where,['t_yz_yzbxxx.sstatus','=','提交']);
        $data = DB::table($this->tbName)
            ->where($where)
            ->join('t_yz_yzjbxx','t_yz_yzjbxx.id','=','t_yz_yzbxxx.yzid')
            ->select('t_yz_yzbxxx.id as id','dbxrq','sbxsx','sbxnr','t_yz_yzjbxx.syzxm','t_yz_yzbxxx.slxdh','sslzt','dslrq','sblqk','dbjrq','dhfrq','syzpj','sbmynr')
            ->orderBy('dbxrq','desc')
            ->paginate($size);
        return $data;
    }

    public function handleRepairInfo($id)
    {
        $res = DB::table($this->tbName)->where('id',$id)->first();
        if($res == null || $res->sslzt !== "未受理")
            return false;
        DB::table($this->tbName)
            ->where('id',$id)
            ->update([
                'sslzt' => "已受理",
                'dslrq' => Carbon::now()
            ]);
        return true;
    }

    public function finishRepairInfo($id,$data)
    {
        $res = DB::table($this->tbName)->where('id',$id)->first();

        if($res == null || $res->sslzt !== "已受理")
            return false;
        DB::table($this->tbName)
            ->where('id',$id)
            ->update($data);
        return true;
    }
    
    public function searchRepairInfoOwner($ownerId,$startTime,$endTime,$size)
    {
        $where=[];
        if (!empty($ownerId)){
            array_push($where,[$this->tbName.'.yzid','=',$ownerId]);
        }
        if (!empty($startTime)){
            array_push($where,[$this->tbName.'.dbxrq','>',$startTime]);
        }
        if (!empty($endTime)){
            array_push($where,[$this->tbName.'.dbxrq','<',$endTime]);
        }

        $data = DB::table($this->tbName)
            ->where($where)
            ->select($this->tbName.'.id as id','dbxrq','sbxsx','sbxnr','sslzt','dslrq','sblqk','dbjrq','syzpj','sbmynr','sstatus')
            ->orderBy('dbxrq','desc')
            ->paginate($size);
        return $data;
    }

    public function getEnterpriseId($ownerId)
    {
        $enterpriseId = DB::table('t_yz_yzjbxx')
            ->where('t_yz_yzjbxx.id',$ownerId)
            ->join('t_xm_jbxx','t_xm_jbxx.id','=','xmid')
            ->select('sqyid')
            ->first()->sqyid;
        return $enterpriseId;
    }

    public function getRepairType()
    {
        $data = DB::table('t_code')
            ->where('dmlbmc','业主报修事项')
            ->select('mc as key','mc as value')
            ->get();
        return $data;
    }

    public function createRepairInfoOwner($userId,$data)
    {
        $enterpriseId = $this->getEnterpriseId($userId);

        $data = array_merge($data,[
            'id' => SqlTool::makeUUID(),
            'yzid' => $userId,
            'qyid' => $enterpriseId,
            'sslzt' => '未受理',
            'sstatus' => '暂存'
        ]);
        $this->create($data);
    }

    public function submitRepairInfo($id)
    {
        $res = DB::table($this->tbName)->where('id',$id)->first();
        if($res == null || $res->sstatus !== "暂存")
            return false;
        DB::table($this->tbName)
            ->where('id',$id)
            ->update([
                'sstatus' => '提交'
            ]);
        return true;
    }

    public function evaluateRepairInfo($id,$data)
    {
        $res = DB::table($this->tbName)->where('id',$id)->first();
        if($res == null || $res->sslzt !== "已办结")
            return false;
        DB::table($this->tbName)
            ->where('id',$id)
            ->update($data);
        return true;
    }

    public function getRepairInfoBy($id)
    {
        $ownerInfo =  DB::table($this->tbName)
            ->where('id',$id)
            ->select('dbxrq','slxdh','sbxsx','sbxnr','sslzt','dslrq','sblqk','dbjrq','dhfrq','syzpj','dpjrq','sbmynr')
            ->first();
        return $ownerInfo;
    }

    public function delete($id)
    {
        $status = DB::table($this->tbName)->where('id', $id)->value('sstatus');
        if ($status === '提交'){
            return 1001;
        }else{
            DB::table($this->tbName)->where('id', $id)->delete();
            return 1000;
        }
    }

    public function update($id, $data)
    {
        $status = DB::table($this->tbName)->where('id', $id)->value('sstatus');
        if ($status === '提交'){
            return 1001;
        }else{
            DB::table($this->tbName)->where('id', $id)->update($data);
            return 1000;
        }
    }

    public function getRepairInfo($id)
    {
        $repqirInfo =  DB::table($this->tbName)
            ->where('t_yz_yzbxxx.id',$id)
            ->join('t_yz_yzjbxx','t_yz_yzjbxx.id','=','t_yz_yzbxxx.yzid')
            ->first();
        return $repqirInfo;
    }

}