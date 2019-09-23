<?php

namespace App\Service\WYXM;

use App\Service\BaseService;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class  XMJJService extends BaseService{

    protected $tbName= 't_xm_xmjj';

    public function getQy($xydm){
        $qy = DB::table('t_qyjbxx')
            ->where('sshxydm','=',$xydm)
            ->select('id','sshxydm','sqymc','sfddbr','dclrq','slxr','slxdh_sj')
            ->first();
        return $qy;
    }

    public function getQyByXm($xmid){
        $qy = DB::table('t_xm_jbxx')
            ->where('t_xm_jbxx.id','=',$xmid)
            ->join('t_qyjbxx','t_xm_jbxx.sqyid','=','t_qyjbxx.id')
            ->select('t_qyjbxx.id','t_qyjbxx.sshxydm','t_qyjbxx.sqymc','t_qyjbxx.sfddbr','t_qyjbxx.dclrq','t_qyjbxx.slxr','t_qyjbxx.slxdh_sj')
            ->first();
        return $qy;
    }

    /**
     * 企业入驻
     * @param $data :{ $xmid,$qyid,$drzsj,$srzjbr,$drzjbsj }
     */
    public function qyRz($data){
        $time = new Carbon();
        $data['drzjbsj'] = $time->toDateTimeString();
        DB::transaction(function ()use ($data){
            DB::table('t_xm_xmjj')
                ->insert($data);
            $szbqymc = DB::table('t_qyjbxx')->where('id',$data['sqyid'])->value('sqymc');
            DB::table('t_xm_jbxx')
                ->where('id',$data['xmid'])
                ->update([
                    'sxmzt'=>'在管',
                    'sqyid'=>$data['sqyid'],
                    'szbqymc'=>$szbqymc
                ]);
        });
    }

    /**
     * 判断项目是否可被接管，可以则返回true，否则返回false
     * @param $xmid
     * @return boolean
     */
    public function isXmCanBeJieGuan($xmid){
        $status = DB::table('t_xm_jbxx')
            ->where('id',$xmid)
            ->value('sxmzt');
        return $status=='待管';
    }


    public function qyTc($data){
        $time = new Carbon();
        $data['dtcjbsj']= $time->toDateTimeString();
        DB::transaction(function ()use ($data){
           DB::table('t_xm_jbxx')
               ->where('id',$data['xmid'])
               ->update([
                   'sqyid'=>null,
                   'sxmzt'=>'待管'
               ]);
           DB::table('t_xm_xmjj')
               ->where([
                   ['xmid','=',$data['xmid']],
                   ['dtcsj','=',null]
               ])->update(
                   $data
               );
        });
    }

    public function getJJJL($xmid){
        $jjjl = DB::table('t_xm_xmjj')
            ->where('xmid',$xmid)
            ->join('t_qyjbxx','t_qyjbxx.id','=',$this->tbName.'.'.'sqyid')
            ->select($this->tbName.'.'.'*','t_qyjbxx.sqymc')
            ->orderBy('drzsj','asc')
            ->get();
        foreach ($jjjl as $item){
            $item->drzsj = SqlTool::getNYRTime($item->drzsj);
            $item->dtcsj = SqlTool::getNYRTime($item->dtcsj);
        }
        return $jjjl;
    }

}