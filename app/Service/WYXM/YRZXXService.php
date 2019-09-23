<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/21
 * Time: 下午1:49
 */

namespace App\Service\WYXM;


use App\Service\BaseService;
use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class YRZXXService extends BaseService
{
    protected $tbName = 't_xm_yrzxx';

    /**
     * 搜索已入住信息
     */
    public function search($sxmbh, $irzqs, $dbqjgsjStart, $dbqjgsjEnd, $dbqrzsjStart, $dbqrzsjEnd, $size)
    {
        $where = [];
        if ($sxmbh !== null && $sxmbh !== '') {
            array_push($where, ['sxmbh', '=', $sxmbh]);
        }

        if ($irzqs !== null && $irzqs !== 0) {
            array_push($where, ['irzqs', '=', $sxmbh]);
        }

        if ($dbqjgsjStart !== null && $dbqjgsjEnd !== null) {
            array_push($where, ['dbqjgsj', '>=', $dbqjgsjStart]);
            array_push($where, ['dbqjgsj', '<=', $dbqjgsjEnd]);
        }

        if ($dbqrzsjStart !== null && $dbqrzsjEnd !== null) {
            array_push($where, ['dbqrzsj', '>=', $dbqrzsjStart]);
            array_push($where, ['dbqrzsj', '<=', $dbqrzsjEnd]);
        }

        $data = DB::table($this->tbName)
            ->where($where)
            ->paginate($size);
        return $data;
    }


    public function getTotalInfo($xmid)
    {
        $info = DB::table($this->tbName)
            ->select('xmid',DB::raw('xmid, sxmbh, SUM(nrzgm) as nrzgm, SUM(ifwzs) as ifwzs,SUM(ifwts) as ifwts,SUM(ihz) as ihz,SUM(igh) as igh,SUM(igjg) as igjg,SUM(iqt) as iqt,SUM(ighlstcw) as ighlstcw,SUM(idstcw) as idstcw,SUM(idxtcw) as idxtcw,SUM(ilstcw) as ilstcw,SUM(idt) as idt,SUM(isssb) as isssb,SUM(idrsb) as idrsb,SUM(ijkxt) as ijkxt,SUM(izsb) as izsb,SUM(ixfsb) as ixfsb,SUM(izysb) as izysb,SUM(izlsb) as izlsb,SUM(ixfs) as ixfs,SUM(ixfsx) as ixfsx,SUM(imhq) as imhq'))
            ->where('xmid','=',$xmid)
            ->groupBy('xmid')
            ->get();
        return $info;
    }

    public function updateINfo($id,$info)
    {
        DB::table($this->tbName)->where('id',$id)->update($info);
    }

    public function searchByXmid($xmid,$size)
    {
        $data=DB::table($this->tbName)
            ->where('xmid',$xmid)
            ->select('id','sxmbh','irzqs','dbqjgsj','dbqrzsj','nrzgm','ifwzs','ifwts')
            ->paginate($size);
        foreach ($data as $item){
            $item->dbqrzsj = SqlTool::getNYRTime($item->dbqrzsj);
            $item->dbqjgsj = SqlTool::getNYRTime($item->dbqjgsj);
        }
        return $data;
    }

    public function showById($id)
    {
        $data=DB::table($this->tbName)
            ->where('id',$id)
            ->first();
        return $data;
    }

    public function delete($id)
    {
        DB::table($this->tbName)->where('id',$id)->delete();
    }
}