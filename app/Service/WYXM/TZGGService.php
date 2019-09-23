<?php
/**
 * Created by PhpStorm.
 * User: plyjdz
 * Date: 18-8-2
 * Time: 下午7:39
 */

namespace App\Service\WYXM;

use App\Service\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TZGGService extends BaseService
{
    protected $tbName = 't_tzgg';
    public function getstatus($id)
    {
        $status=DB::table($this->tbName)->where('id',$id)->value('sstatus');
        return $status;
    }

    public function getTzgg($id)
    {
        $data=DB::table($this->tbName)->where('id',$id)->first();
        return $data;
    }

    public function publish($id)
    {
        $dfbsj=Carbon::now();
        DB::table($this->tbName)->where('id',$id)->update([
            'sstatus'=>'提交',
            'dfbsj'=>$dfbsj
        ]);
    }

    public function getType(string $type)
    {
        $types = [
            'fgj' => '房管局',
            'xm' => '项目',
            'ywh' => '业委会'
        ];
        try {
            $res = $types[$type];
        } catch (\Exception $exception) {
            return null;
        }
        return $res;
    }

    /**
     * @param $type string 通知公告类型
     * @param $xzqh string 类别:房管局、项目、业委会
     * @param $title string 标题
     * @param $time string 发布时间
     * @param $persion string 发布人
     * @param $size int 分页大小
     * @param $nowUser string 当前用户类型
     * @return mixed
     */
    public function search($type,$xzqh,$title,$time,$persion,$size,$nowUser)
    {

        $where = [];
        if (isset($type)) {
            $where[] = ['slb', '=', $type];
        }
        if(isset($xzqh)) {
            $where[] = ['sxzqh', 'like', substr($xzqh,0,6).'%'];
        }
        if (isset($title)) {
            $where[] = ['sbt', 'like','%'.$title.'%'];
        }
        if (isset($time)) {
            $where[] = ['dfbsj', '=', $time];
        }
        if (isset($persion)) {
            $where[] = ['sfbr', '=', $persion];
        }
        $data = DB::table($this->tbName)
            ->where($where)
            ->where('sif'.$nowUser,'是')
            ->paginate($size);
        return $data;
    }
}