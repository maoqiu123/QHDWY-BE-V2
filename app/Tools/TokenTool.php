<?php

namespace App\Tools;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TokenTool
{
    public static $config=[
        'fgj'=>[
            'table_name'=>'t_fgj',
            'login_column_name'=>'login_name',
            'password_column_name'=>'password',
            'select'=>['t_fgj.id','t_fgj.smc','t_fgj.sxzqh','t_fgj.jc','t_fgj.login_name','t_fgj.status','t_fgj.login_name as sdlzh','t_fgj.role']
        ],
        'qy'=>[
            'table_name'=>'t_qyjbxx',
            'login_column_name'=>'sdlzh',
            'password_column_name'=>'sdlmm',
            'select'=>['t_qyjbxx.id','t_qyjbxx.id as qyid','t_qyjbxx.sqymc','t_qyjbxx.sxzqh','t_qyjbxx.sshxydm','t_qyjbxx.sdlzh','t_qyjbxx.sqymc as smc','t_qyjbxx.sxzqh']
        ],
        'ywh'=>[
            'table_name'=>'t_ywh_jbxx',
            'login_column_name'=>'sdlzh',
            'password_column_name'=>'sdlmm',
            'select'=>['t_ywh_jbxx.id','t_ywh_jbxx.xmid','t_ywh_jbxx.sdlzh','t_ywh_jbxx.sdlzh as smc']
        ],
        'xm'=>[
            'table_name'=>'t_xm_jbxx',
            'login_column_name'=>'sdlzh',
            'password_column_name'=>'sdlmm',
            'select'=>['t_xm_jbxx.id','t_xm_jbxx.sxmbh','t_xm_jbxx.sxmmc','t_xm_jbxx.sqyid','t_xm_jbxx.sssqx as sxzqh','t_xm_jbxx.sdlzh','t_xm_jbxx.sxmmc as smc','t_xm_jbxx.skfjsdw']
        ],
        'yz'=>[
            'table_name'=>'t_yz_yzjbxx',
            'login_column_name'=>'sdlzh',
            'password_column_name'=>'sdlmm',
            'select'=>['t_yz_yzjbxx.id','t_yz_yzjbxx.xmid','t_yz_yzjbxx.syzxm','t_yz_yzjbxx.slxdh','t_yz_yzjbxx.sdlzh','t_yz_yzjbxx.syzxm as smc','t_yz_yzjbxx.sd','t_yz_yzjbxx.sdy','t_yz_yzjbxx.sh']
        ],
        'yzzh'=>[
            'table_name'=>'t_yz_yzzh',
            'login_column_name'=>'slxdh',
            'password_column_name'=>'sdlmm',
            'select'=>['t_yz_yzzh.id','t_yz_yzzh.slxdh']
        ],
        'jsdw'=>[
            'table_name'=>'t_jsdw_jbxx',
            'login_column_name'=>'sdlzh',
            'password_column_name'=>'sdlmm',
            'select'=>['t_jsdw_jbxx.id','t_jsdw_jbxx.sqymc','t_jsdw_jbxx.sdlzh','t_jsdw_jbxx.sqymc as smc']
        ]
        ];

    /**
     * @param $userId
     * @param string $type
     * @return string
     */
    public static function makeToken($userId, string $type)
    {
        $time=Carbon::now();
//        $Token=self::$config[$type]['table_name'].':'.md5(SqlTool::generatePassword(6).$time.$userId);
        $Token=md5(SqlTool::generatePassword(6).$time.$userId);
        DB::table('token')->insert([
            'content'=>$Token,
//            'user_table'=>self::$config[$type]['table_name'],
            'user_id'=>$userId,
            'expired_at'=>$time->addDay(1),
            'created_at'=>$time
        ]);
        return $Token;
    }

    public static function isTokenExpired($token){
        $expired_at=DB::table('token')->where('content','=',$token)->value('expired_at');
         return Carbon::now()>$expired_at;
    }

    public static function isTokenExist($token){
        $res=DB::table('token')->where('content','=',$token)->first();
        if ($res){
            return true;
        }
        else{
            return false;
        }
    }

    public static function getUserByToken($token,$type){
        $user=DB::table('token')->where('content','=',$token)
            ->join(self::$config[$type]['table_name'],'token.user_id','=',self::$config[$type]['table_name'].'.'.'id')
            ->select(self::$config[$type]['select'])
            ->first();
        return $user;
    }

    public static function isYzzhTokenExist($token){
        $res=DB::table('t_verification_code')->where('token','=',$token)->first();
        if ($res){
            return true;
        }
        else{
            return false;
        }
    }
    public static function isYzzhTokenExpired($token){
        $expired_at=DB::table('t_verification_code')->where('token','=',$token)->value('expired_at');
        return Carbon::now()>$expired_at;
    }
    public static function getYzzhUserByToken($token,$type){
        $user=DB::table('t_verification_code')->where('token','=',$token)
            ->first();
        return $user->slxdh;
    }

}