<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/24
 * Time: 上午9:34
 */

namespace App\Service\XCKP;


use App\Service\BaseService;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JCJLService extends BaseService
{
    protected $tbName = 't_xckp_jcjl';

    public function isAllocled($taskId, $projectId)
    {
        $flag = DB::table('t_xckp_xmfp')->where([
            ['srwid', '=', $taskId],
            ['sxmid', '=', $projectId]
        ])->first();
        if ($flag == null)
            return false;
        return true;
    }

    public function changeAllocledStatus($taskId, $projectId, $status)
    {
        DB::table('t_xckp_xmfp')->where([
            ['srwid', '=', $taskId],
            ['sxmid', '=', $projectId]
        ])->update([
            'sstatus' => $status
        ]);
    }

    public function isNotSelf($taskId)
    {
        $flag = DB::table('t_xckp_jcrw')->where([
            ['id', '=', $taskId],
            ['ssfzj', '=', "否"]
        ])->first();
        if ($flag == null)
            return false;
        return true;
    }


    public function searchInspectionResult($taskId, $projectId, int $size, $params = '', array $other = [])
    {
        if ($params == '')
            $params = '' . $this->tbName . '.*';
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
            ->where($other)
            ->orderBy('t_xckp_jcdx.id', 'asc')
            ->orderBy('t_xckp_jcdx.sdxbh', 'asc')
            ->select('t_xckp_jcdx.sdxmc', 't_xckp_jcbz.sjcbzwb', 't_xckp_jcbz.id as sjcbzid', $this->tbName . '.id as id',
                DB::raw($params))//"{$params}"
            ->paginate($size);

        return $data;
    }

    public function checkInspectionResult($taskId, $enterpriseId, $projectId, $standId)
    {
        $res = DB::table($this->tbName)->where([
            ['srwid', '=', $taskId],
            ['sqyid', '=', $enterpriseId],
            ['sxmid', '=', $projectId],
            ['sjcbzid', '=', $standId]
        ])->first();
        if ($res != null)
            return $res->sjczt === "已提交" ? -1 : 0;
        return 1;
    }


    public function saveInspectionResult($taskId, $enterpriseId, $projectId, array $data, $inputPerson)
    {
        $res = [];
        foreach ($data as $row) {
            $checkRes = $this->checkInspectionResult($taskId, $enterpriseId, $projectId, $row['sjcbzid']);
            switch ($checkRes) {
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
                    $res[] = array_merge($row, [
                        'id' => SqlTool::makeUUID(),
                        'srwid' => $taskId,
                        'sqyid' => $enterpriseId,
                        'sxmid' => $projectId,
                        'slrr' => $inputPerson,
                        'sjczt' => '未提交',
                        'dlrsj' => Carbon::now()
                    ]);
                    break;
            }
        }
        $this->create($res);
        $this->changeAllocledStatus($taskId, $projectId, '已录入');
    }

    public function submitInspectionResult(array $rowIdData)
    {
        DB::table($this->tbName)->whereIn('id', $rowIdData)->update([
            'sjczt' => '已提交'
        ]);
    }

    public function getInspectionTask()
    {
        $res = DB::table('t_xckp_xmfp')
            ->join('t_xckp_jcrw', 't_xckp_jcrw.id', '=', 't_xckp_xmfp.srwid')
            ->where('ssfzj', "否")
            ->distinct('t_xckp_jcrw.id')
            ->select('srwmc', 't_xckp_jcrw.id')
            ->get();

        return $res;
    }

    public function getInspectionProject($taskId)
    {
        $res = DB::table('t_xckp_xmfp')
            ->where('srwid', $taskId)
            ->join('t_xm_jbxx', 't_xm_jbxx.id', '=', 'sxmid')
            ->select('t_xm_jbxx.sxmmc', 't_xm_jbxx.id')
            ->get();
        return $res;
    }

    public function getUnqualifiedProject($taskId,$submitStatus=[])
    {
        $unqualifiedRecord = DB::table($this->tbName)
            ->where([
                ['srwid', '=', $taskId],
                ['sjcjg', '=', "不合格"],
                $submitStatus
            ])
            ->distinct()
            ->pluck('sxmid')
            ->toArray();
        $res = DB::table('t_xckp_xmfp')
            ->where([
                ['srwid', '=', $taskId],
//                ['t_xckp_xmfp.sstatus', '=', $submitStatus]
            ])
            ->join('t_xm_jbxx', 't_xm_jbxx.id', '=', 'sxmid')
            ->whereIn('sxmid', $unqualifiedRecord)
            ->select('t_xm_jbxx.sxmmc', 't_xm_jbxx.id')
            ->get();
        return $res;
    }

    public function printNotice(array $recordIds)
    {
        $res1 = DB::table($this->tbName)
            ->whereIn('id',$recordIds)
            ->orderBy('djcsj','desc')
            ->first();

        $res2 = DB::table($this->tbName)
            ->whereIn('id',$recordIds)
            ->orderBy('dzgsx','desc')
            ->first();

        $res3 = DB::table($this->tbName)
            ->whereIn('id',$recordIds)
            ->orderBy('dzgsx','desc')
            ->pluck('swtms')
            ->toArray();
        $res4 = implode(",",$res3);


        $taskId = $res1->srwid;
        $taskName = DB::table('t_xckp_jcrw')->where('id',$taskId)->select('srwmc')->first()->srwmc;
        $enterpriseId = $res1->sqyid;
        $enterpriseName = DB::table('t_qyjbxx')->where('id',$enterpriseId)->select('sqymc')->first()->sqymc;

        $lastCheckTime = $res1->djcsj;
        $lastRectifyTime = $res2->dzgsx;

        $data = [
            'task_name' => $taskName,
            'enterprise_name' => $enterpriseName,
            'last_check_time' => $lastCheckTime,
            'last_rectify_time' => $lastRectifyTime,
            'problems' => $res4,
            'time' => substr((string)(Carbon::now()),10)
        ];
        foreach ($recordIds as $recordId)
        {
            $isExist = DB::table($this->tbName)
                ->where('id',$recordId)
                ->first();
            if(!$isExist->szgtzsbh)
                DB::table($this->tbName)
                    ->where('id',$recordId)
                    ->update([
                    'szgtzsbh' => SqlTool::makeUUID(),
                    'id' => $recordId
                ]);
        }

        return $data;
    }

    public function saveResultEntry(array $data)
    {
        foreach ($data as $row) {
                    $id = $row['id'];
                $isSubmit = DB::table($this->tbName)
                    ->where('id',$id)
                    ->first();
                    if($isSubmit->szgzt!='已提交')
                    {
                        $row['szgzt'] = '已保存';
                        unset($row['id']);
                        DB::table($this->tbName)
                            ->where('id',$id)
                            ->update($row);
                    }
        }
    }

    public function submitResultEntry(array $rowIdData)
    {
        DB::table($this->tbName)->whereIn('id', $rowIdData)->update([
            'szgzt' => '已提交'
        ]);
    }

    public function getInspectionSituation($taskId)
    {
        //todo
    }

}