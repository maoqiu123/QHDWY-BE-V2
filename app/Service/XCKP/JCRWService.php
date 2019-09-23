<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/18
 * Time: 下午2:44
 */

namespace App\Service\XCKP;

use App\Service\BaseService;
use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class JCRWService extends BaseService
{
    protected $tbName = 't_xckp_jcrw';

    public function searchInspectionTask($taskId, $taskName,
                                        $taskType, $regionId,int $size)
    {

        $where = [];
        if ($taskId !== null) {
            array_push($where, ['srwbh', '=', $taskId]);
        }

        if ($taskName !== null) {
            array_push($where, ['srwmc', 'like', '%' . $taskName . '%']);
        }

        if ($taskType !== null) {
            array_push($where, ['srwlx', '=',  $taskType]);
        }

        $data = DB::table($this->tbName)
            ->where('sxzqh', $regionId)
            ->select('id', 'srwbh','srwmc','srwlx','sxzqh','ssfzj')
            ->where($where)
            ->paginate($size);
        return $data;
    }

    public function deleteInspectionTask($id)
    {
        $res = DB::table('t_xckp_jcdx')->where('t_xckp_jcdx.srwid',$id)
            ->leftjoin('t_xckp_jcbz','t_xckp_jcbz.sdxid','=','t_xckp_jcdx.id')
            ->leftjoin('t_xckp_qyzj','t_xckp_qyzj.sjcbzid','=','t_xckp_jcbz.id')
            ->leftjoin('t_xckp_jcjl','t_xckp_jcjl.sjcbzid','=','t_xckp_jcbz.id')
            ->count();
        if($res > 0)
            return false;
        $this->delete($id);
        return true;
    }

    public function getAllocaledTask($taskId)
    {
        $data = DB::table('t_xckp_xmfp')
            ->where('srwid','=',$taskId)
            ->join('t_xm_jbxx','t_xm_jbxx.id','=','t_xckp_xmfp.sxmid')
            ->select('t_xm_jbxx.id as id','t_xm_jbxx.sxmbh','t_xm_jbxx.sxmmc','t_xm_jbxx.sxmlx')
            ->get();
        return $data;
    }

    public function searchAllocalTask($taskId,$projectName, $projectType,int $size)
    {
        $where = [];

        if ($projectName !== null) {
            array_push($where, ['sxmmc', 'like', '%' . $projectName . '%']);
        }

        if ($projectType !== null) {
            array_push($where, ['sxmlx', 'like', '%' . $projectType . '%']);
        }

        $projects = DB::table('t_xckp_xmfp')
            ->where('srwid',$taskId)
            ->pluck('sxmid')->toArray();
        $data = DB::table('t_xm_jbxx')
            ->where($where)
            ->whereNotIn('id',$projects)
            ->select('id', 'sxmbh','sxmmc','sxmlx')
            ->paginate($size);
        return $data;
    }

    public function saveAllocalStatus($taskId, array $projects)
    {
        $oldData = array_column($this->getAllocaledTask($taskId)->toArray(),'id');
        $both = array_intersect($oldData,$projects);
        $add = array_diff($projects,$both);
        $remove = array_diff($oldData,$both);
        $flag = false;
        DB::transaction(function () use ($taskId,$add,$remove,&$flag) {

            DB::table('t_xckp_xmfp')
                ->where('srwid',$taskId)
                ->whereIn('sxmid',$remove)
                ->delete();
            $data = [];
            foreach($add as $key => $value)
            {
                $data[]=[
                    'id' => SqlTool::makeUUID(),
                    'srwid' => $taskId,
                    'sxmid' => $value,
                    'sstatus' => "未录入"
                ];
            }
            DB::table('t_xckp_xmfp')->insert($data);
            $flag =true;
        });
        return $flag;
    }

    public function getInspectionTask()
    {
        $res = DB::table('t_xckp_jcrw')->select('srwmc','id')->get();
        return $res;
    }
}