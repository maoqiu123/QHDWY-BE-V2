<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午10:15
 */

namespace App\Service\LJXQ;

use App\Service\BaseService;
use Illuminate\Support\Facades\DB;

class GZGZJDJZQKService extends BaseService
{
    protected $tbName = 't_ljxq_gzgzjd_jzqk';
    public function searchGzgzjdjzqk($sgzjhid,int $size){
        $datas = DB::table($this->tbName)
            ->select('id','dsbrq','sjzqk', 'ffj','ssjzt')
            ->where('sgzjhid',$sgzjhid)
            ->paginate($size);
        foreach ($datas as $data) {
            $data->ffj = json_decode($data->ffj);
            if ($data->ffj != null) {
                foreach ($data->ffj as $key => $value)
                    $data->ffj->$key = json_decode($value);
            }
        }
        return $datas;
    }
    public function getstatus($id)
    {
        $status=DB::table($this->tbName)->where('id',$id)->value('ssjzt');
        return $status;
    }
}