<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/16
 * Time: 下午4:48
 */

namespace App\Service\WYQY;

use App\Service\BaseService;
use App\Tools\DtcxTool;
use Illuminate\Support\Facades\DB;

class QYJBXXWHService extends BaseService
{
    protected $tbName = 't_qyjbxx';

    public function searchEnterprise($enterpriseCode, $enterpriseName, $regionId,
                                     $contacts, $xzqh,int $size, $select = ['*'])
    {
        $xzqh = DtcxTool::getXzqhTrueStr($xzqh);
        $where = [];
        array_push($where,['sxzqh','like',$xzqh.'%']);
        if ($enterpriseCode !== null) {
            array_push($where, ['sshxydm', '=', $enterpriseCode]);
        }

        if ($enterpriseName !== null && $enterpriseName !== '') {
            array_push($where, ['sqymc', 'like', '%' . $enterpriseName . '%']);
        }

        if ($regionId !== null) {
            array_push($where, ['sxzqh', 'like', $regionId. '%']);
        }

        if ($contacts !== null) {
            array_push($where, ['sfddbr', 'like', '%' . $contacts . '%']);
        }


        $data = DB::table($this->tbName)
            ->select($select)
            ->where($where)
            ->paginate($size);
        return $data;
    }

    public function deleteEnterprise($id): bool
    {
//        $data = $this->show([
//            ['id', '=', $id]
//        ]);
//        $data = $data->first();
//        if (isset($data->sbgdz) && $data->sbgdz !== '')
//            return false;
        $dataCount =
            DB::table('t_xm_jbxx')->where('t_xm_jbxx.sqyid','=',$id)->count() +
            DB::table('t_qyjbxx_glry')->where('t_qyjbxx_glry.qyid','=',$id)->count() +
            DB::table('t_qyjbxx_jcjl')->where('t_qyjbxx_jcjl.qyid','=',$id)->count() +
            DB::table('t_qyjbxx_nsxx')->where('t_qyjbxx_nsxx.qyid','=',$id)->count() +
            DB::table('t_qyjbxx_wbgs')->where('t_qyjbxx_wbgs.qyid','=',$id)->count() +
            DB::table('t_qyjbxx_wbxm')->where('t_qyjbxx_wbxm.qyid','=',$id)->count() +
            DB::table('t_qyjbxx_zzxx')->where('t_qyjbxx_zzxx.qyid','=',$id)->count();
        if ($dataCount > 0)
            return false;
        $this->delete($id);
        return true;
    }

    public function getstatus($id)
    {
        $status=DB::table($this->tbName)->where('id',$id)->select('sstatus')->first();
        return $status;
    }

    public function updates(array $ids, array $data)
    {
        DB::table($this->tbName)->whereIn('id', $ids)->update($data);
    }

    public function getQyxxByQyId($id)
    {
        $data = DB::table($this->tbName)
            ->where('id', '=', $id)
            ->first();
        return $data;
    }
    public function showByQy($id)
    {
        $data=DB::table($this->tbName)->where('id',$id)->first();
        return $data;
    }

    public function getXzqh(array $usedArea)
    {
        $datas = DB::table('t_xzqh')->whereIn('bm',$usedArea)->select('bm','mc')->get()->toArray();
        $xzqh = [];
        foreach ($datas as $data)
        {
            $xzqh[$data->bm] =  $data->mc;
        }
        return $xzqh;
    }

    public function getForYwh($id)
    {
        $xmid=DB::table('t_ywh_jbxx')->where('id',$id)->value('xmid');
        $qyid=DB::table('t_xm_jbxx')->where('id',$xmid)->value('sqyid');
        $data=DB::table('t_qyjbxx')->where('id',$qyid)->get();
        return $data;
    }

    public function getXmxxForYwh($id)
    {
        $xmid=DB::table('t_ywh_jbxx')->where('id',$id)->value('xmid');
        $data=DB::table('t_xm_jbxx')->where('id',$xmid)->get();
        return $data;
    }

    public function getxzqhbm($xzqh)
    {
        $data=DB::table('t_code')->where('mc',$xzqh)->value('bm');
        return $data;
    }
}