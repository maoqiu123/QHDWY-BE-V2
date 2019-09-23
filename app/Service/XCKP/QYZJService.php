<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/23
 * Time: 下午1:58
 */

namespace App\Service\XCKP;


use App\Service\BaseService;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QYZJService extends BaseService
{
    protected $tbName = 't_xckp_qyzj';

    public function isAllocled($taskId,$projectId)
    {
        $flag = DB::table('t_xckp_xmfp')->where([
            ['srwid', '=', $taskId],
            ['sxmid', '=', $projectId]
        ])->first();
        if($flag == null)
            return false;
        return true;
    }

    public function changeAllocledStatus($taskId,$projectId,$status)
    {
        DB::table('t_xckp_xmfp')->where([
            ['srwid', '=', $taskId],
            ['sxmid', '=', $projectId]
        ])->update([
            'sstatus' => $status
        ]);
    }

    public function isSelf($taskId)
    {
        $flag = DB::table('t_xckp_jcrw')->where([
            ['id', '=', $taskId],
            ['ssfzj', '=', "是"]
        ])->first();
        if($flag == null)
            return false;
        return true;
    }


    public function searchSelfRecord($taskId, $projectId, int $size)
    {
        $where = [];
        if ($taskId !== null) {
            array_push($where, ['t_xckp_jcdx.srwid', '=', $taskId]);
        }
        $data = DB::table('t_xckp_jcdx')
            ->where($where)
            ->join('t_xckp_jcbz', 't_xckp_jcbz.sdxid', '=', 't_xckp_jcdx.id')
            ->leftJoin($this->tbName, function ($join) use ($taskId, $projectId) {
                $join->on('t_xckp_jcbz.id', '=', $this->tbName . '.sjcbzid')
                    ->where($this->tbName . '.sxmid', '=', $projectId);
            })
            ->orderBy('t_xckp_jcdx.id', 'asc')
            ->orderBy('t_xckp_jcdx.sdxbh', 'asc')
            ->select('t_xckp_jcdx.sdxmc', 't_xckp_jcbz.sjcbzwb', $this->tbName . '.*', 't_xckp_jcbz.id as sjcbzid', $this->tbName . '.id as id')
            ->paginate($size);

        return $data;
    }

    public function checkSelfRecord($taskId, $enterpriseId, $projectId, $standId)
    {
        $res = DB::table($this->tbName)->where([
            ['srwid', '=', $taskId],
            ['sqyid', '=', $enterpriseId],
            ['sxmid', '=', $projectId],
            ['sjcbzid', '=', $standId]
        ])->first();
        if ($res != null)
            return $res->sstatus === "已提交" ? -1 : 0;
        return 1;
    }


    public function saveSelfRecord($taskId, $enterpriseId, $projectId, array $data, $inputPerson)
    {
        $res = [];
        foreach ($data as $row)
        {
            $checkRes = $this->checkSelfRecord($taskId, $enterpriseId, $projectId,$row['sjcbzid']);
            switch ($checkRes)
            {
                case -1:
                    break;
                case 0:
                    DB::table($this->tbName)
                        ->where([
                            ['srwid', '=', $taskId],
                            ['sqyid', '=', $enterpriseId],
                            ['sxmid', '=', $projectId],
                            ['sjcbzid', '=', $row['sjcbzid']]
                        ])
                        ->update($row);
                    break;
                case 1:
                    $res[]  = array_merge($row,[
                        'id' => SqlTool::makeUUID(),
                        'srwid' => $taskId,
                        'sqyid' => $enterpriseId,
                        'sxmid' => $projectId,
                        'slrr' => $inputPerson,
                        'sstatus' => "未提交",
                        'dlrsj' => Carbon::now()
                    ]);
                    break;
            }
        }
        $this->create($res);
        $this->changeAllocledStatus($taskId,$projectId,'已录入');
    }

    public function submitSelfRecord(array $rowIdData)
    {
        DB::table($this->tbName)->whereIn('id',$rowIdData)->update([
            'sstatus' => "已提交"
        ]);
    }

    public function getInspectionTask()
    {

        $res = DB::table('t_xckp_xmfp')
            ->join('t_xckp_jcrw','t_xckp_jcrw.id','=','t_xckp_xmfp.srwid')
            ->where('ssfzj',"是")
            ->distinct('t_xckp_jcrw.id')
            ->select('srwmc','t_xckp_jcrw.id')
            ->get();

        return $res;
    }

    public function getInspectionProject($taskId)
    {
        $res = DB::table('t_xckp_xmfp')
            ->where('srwid',$taskId)
            ->join('t_xm_jbxx','t_xm_jbxx.id','=','sxmid')
            ->select('t_xm_jbxx.sxmmc','t_xm_jbxx.id')
            ->get();
        return $res;
    }
}