<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/25
 * Time: 下午12:03
 */

namespace App\Service\GWZJ;


use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class SPGCService extends BaseService
{
    protected $tbName = 't_gw_xmspgc';

    public function getApproveRecord($projectId)
    {
        $data = DB::table($this->tbName)
            ->where('xmid',$projectId)
            ->select('id','sspdm','sspsx','dyspwcrq','dsjsprq','ncqts','sspzt','sspqksm')
            ->get();
        return $data;

    }

}