<?php

namespace App\Tools;

use Illuminate\Support\Facades\DB;

class SqlTool
{
    /**
     * 生成唯一ID
     * @return mixed
     */
    static public function makeUUID()
    {
        $uuid = DB::select("select nextval('seed')")[0];
        return array_values(self::object_to_array($uuid))[0];
    }


    static public function makeZZBM()
    {
        $xmbm = DB::select("select nextval('xmbm')")[0];
        return array_values(self::object_to_array($xmbm))[0];
    }

    static public function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }
        return $obj;
    }

    static public function generatePassword($length){
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $password = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }

        return $password;
    }
    static public function getXzqhMcByBm($xzqhbm){
        $xzqh =DB::table('t_xzqh')
            ->where('bm',$xzqhbm)
            ->value('mc');
        return $xzqh;
    }
    static public function getCodeBmBymc($mc){
        $xzqh =DB::table('t_code')
            ->where('mc',$mc)
            ->value('bm');
        return $xzqh;
    }


    static public function getCodeWithMc($mc){
        $dmb=DB::table('t_code')
            ->where('dmlbmc',$mc)
            ->select('mc as value','mc as label')
            ->get()->toArray();
        return $dmb;
    }

    static public function getNYRTime($str){
        $str = substr($str,0,11);
        return $str;
    }

    /**
     * @param $ownerId $业主id
     * @return mixed 企业id
     * 获取用户所属项目的企业id
     */
    public static function getEnterpriseId($ownerId)
    {
        $enterpriseId = DB::table('t_yz_yzjbxx')
            ->where('t_yz_yzjbxx.id', $ownerId)
            ->join('t_xm_jbxx','t_xm_jbxx.id','=','xmid')
            ->value('sqyid');
        return $enterpriseId;
    }

    public static function getFileJsonDecode($data)
    {
        $data->ffj = json_decode($data->ffj);
        if ($data->ffj != null) {
            foreach ($data->ffj as $key => $value)
                $data->ffj->$key = json_decode($value);
        }
        return $data;
    }

    public static function getFilesJsonDecode($list)
    {
        foreach ($list as $data) {
            //todo find the Recursion detected bug
            //$data->ffj = self::getFileJsonDecode($data);
            $data->ffj = json_decode($data->ffj);
            if ($data->ffj != null) {
                foreach ($data->ffj as $key => $value)
                    $data->ffj->$key = json_decode($value);
            }
        }
        return $list;
    }

}