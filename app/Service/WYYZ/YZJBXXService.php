<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/25
 * Time: 下午4:48
 */

namespace App\Service\WYYZ;


use App\Service\BaseService;
use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class YZJBXXService extends BaseService
{
    protected $tbName = 't_yz_yzjbxx';

    public function searchOwnerInfo($xmid,$sd,$sdy,$sh,$size)
    {
        $where=[];
        if (!empty($xmid)){
            array_push($where,['t_yz_yzjbxx.xmid','=',$xmid]);
        }
        if (!empty($sd)){
            array_push($where,['t_yz_yzjbxx.sd','=',$sd]);
        }
        if (!empty($sdy)){
            array_push($where,['t_yz_yzjbxx.sdy','=',$sdy]);
        }
        if (!empty($sh)){
            array_push($where,['t_yz_yzjbxx.sh','=',$sh]);
        }
        $data = DB::table($this->tbName)
            ->where($where)
            ->select('id','sd','sdy','sh','syzxm','slxdh','ssfdlg')
            ->orderBy('sd')
            ->orderBy('sdy')
            ->orderBy('sh')
            ->paginate($size);
        return $data;
    }

    public function deleteOwnersInfo(array $ownersIds)
    {
        $success = [];
        foreach ($ownersIds as $ownerId)
        {
            $flag = false;
            $complainCounts = DB::table('t_yz_yztsxx')->where('yzid',$ownerId)->count();
            $repairCounts = DB::table('t_yz_yzbxxx')->where('yzid',$ownerId)->count();
            $suggestCounts = DB::table('t_yz_yzjyxx')->where('yzid',$ownerId)->count();

            if($complainCounts + $repairCounts + $suggestCounts == 0)
            {
                DB::table($this->tbName)->delete($ownerId);
                $flag = true;
            }
            $success[] = [
                $ownerId => $flag
            ];
        }
        return $success;
    }

    public function initPassword(array $ownersIds)
    {
        DB::table($this->tbName)
            ->whereIn('id',$ownersIds)
            ->update([
                'sdlmm' => 123456,
                'ssfdlg' => '否'
            ]);
    }

    public function isOwnerExist($xmid,$sd,$sdy,$sh)
    {
        $res = DB::table($this->tbName)
            ->where([
                ['xmid','=',$xmid],
                ['sd','=',$sd],
                ['sdy','=',$sdy],
                ['sh','=',$sh],
            ])
            ->first();
        if($res === null)
            return false;
        return true;
    }

    public function createOwnersInfo($data)
    {
        if($this->isOwnerExist($data['xmid'],$data['sd'],$data['sdy'],$data['sh']))
            return false;
        $data = array_merge($data,[
           'id' => SqlTool::makeUUID(),
           'sdlmm' => md5(123456),
           'ssfdlg' => '否',
            'sdlzh' => $data['slxdh']
        ]);
        $this->create($data);
        return true;
    }

    public function showXmidByYzid($id)
    {
        $xmid=DB::table('t_yz_yzjbxx')->where('id',$id)->value('xmid');
        return $xmid;
    }

    public function showQyidByXmid($xmid)
    {
        $qyid=DB::table('t_xm_jbxx')->where('id',$xmid)->value('sqyid');
        return $qyid;
    }

    public function showQyjbxx($qyid)
    {
        $qyjbxx=DB::table('t_qyjbxx')
                ->where('id',$qyid)
                ->select('sshxydm','sqymc','sbgdz','slxr','nbgdzjd','nbgdzwd','slxdh_gh','slxdh_sj','ssfkfjsdwps','sjsdwmc','sxzqh','sfddbr','sfrsfzjhm','sfrlxdh','sdjzclx','sjyfw','szzdj','szzzh','nzczj','sdzyx','sqywz','ssfbsqy','dwbjqrq','ssfxhcydw','drhrq')
                ->get();
        return $qyjbxx;
    }

    public function showXmjbxx($xmid)
    {
        $xmjbxx=DB::table('t_xm_jbxx')
                ->where('id',$xmid)
                ->first();
        return $xmjbxx;
    }
}