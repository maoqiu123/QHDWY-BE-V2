<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/25
 * Time: 上午11:59
 */

namespace App\Service\GWZJ;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class SPXMService extends BaseService
{
    protected $tbName = 't_gw_spxm';

    public function searchProject($region, $endStatus, int $size)
    {
        $where = [];
        if ($region !== null) {
            array_push($where, ['sxzqh', '=', $region]);
        }
        if ($endStatus !== null) {
            array_push($where, ['sbjbz', '=', $endStatus]);
        }

        $data = DB::table($this->tbName)
            ->select('id','sxzqh', 'snd', 'sbh', 'swxxm', 'sbjbz', 'djjrq', 'sspsx', 'dyspwcrq', 'ncqts')
            ->where($where)
            ->paginate($size);
        return $data;
    }

    public function showProject($id)
    {
        $res = DB::table('t_gw_spxm')
            ->where('id', $id)
            ->select('id',"sxzqh", "snd", "sbh", "sfwzl","ssbdwmc","sxmlb","sfwlb","swxxm","ssblxr","ssblxdh","ssgdwmc", "ssglxr","ssglxdh",                "ssgdwxqfs", "nnsyje", "ssgnr", "sjjr", "djjrq", "sbjbz", "dbjrq")
            ->first();
        return $res;
    }



}