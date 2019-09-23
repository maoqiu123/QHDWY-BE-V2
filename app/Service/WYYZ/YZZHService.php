<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/10/18
 * Time: 21:08
 */

namespace App\Service\WYYZ;

use App\Service\BaseService;
use App\Tools\SqlTool;
use App\Tools\TokenTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class YZZHService extends BaseService
{
    public function isPhoneExist(string $login_name, string $type)
    {
        $res = DB::table(TokenTool::$config[$type]['table_name'])
            ->where(TokenTool::$config[$type]['login_column_name'], '=', $login_name)
            ->first();
        return $res !== null;
    }
    public function createAccountYz($userInfo)
    {
            $data['id'] = SqlTool::makeUUID();
            $data['slxdh'] = $userInfo['login_name'];
            $password = $userInfo['password'];
            $data['sdlmm'] = md5($password);
            DB::table('t_yz_yzzh')->insert($data);
            $user['id'] =  $data['id'];
            $user['login_name'] = $userInfo['login_name'];
            $user['password'] = $userInfo['password'];
            return $user;
    }
    public function sendCode($login_name){
        $code = rand(1000,9999);
        $time = Carbon::now()->addHours(1);
        $yzzh = DB::table('t_verification_code')->where('slxdh',$login_name)->first();
        if ($yzzh == null){
            $status = DB::table('t_verification_code')->insert([
                'slxdh' => $login_name,
                'code' => $code,
                'expired_at' => $time
            ]);
        }else{
            $status = DB::table('t_verification_code')->update([
                'slxdh' => $login_name,
                'code' => $code,
                'expired_at' => $time
            ]);
        }
        return $status;
    }
    public function verifyCode($phone,$code){
        $verification = DB::table('t_verification_code')->where('slxdh',$phone)->first();
        if ($verification == null){
            return -1;
        }elseif ($verification->code != $code){
            return -2;
        }elseif ($verification->expired_at < Carbon::now()){
            return -3;
        }else{
            return 0;
        }
    }
    public function isPasswordRight($login_name, $password,string $type)
    {
        $truePassword = DB::table(TokenTool::$config[$type]['table_name'])
            ->where(TokenTool::$config[$type]['login_column_name'],'=', $login_name)
            ->value(TokenTool::$config[$type]['password_column_name']);
        return $truePassword == md5($password);
    }
    public function showBuildings($login_name,string $type){
        $buildings = DB::table('t_yz_yzjbxx')
            ->where(TokenTool::$config[$type]['login_column_name'],'=', $login_name)
            ->get();
        return json_decode($buildings);
    }
    public function makeYzzhToken($login_name){
        $time=Carbon::now();
//        $Token=self::$config[$type]['table_name'].':'.md5(SqlTool::generatePassword(6).$time.$userId);
        $token=md5(SqlTool::generatePassword(6).$time.$login_name);
        DB::table('t_verification_code')
            ->where('slxdh','=', $login_name)
            ->update([
                'token'=>$token,
                'expired_at'=> Carbon::now()->addHours(1)
            ]);
        return $token;
    }
    public function getXmmc($xmid){
        return DB::table('t_xm_jbxx')->where('id','=', $xmid)->value('sxmmc');
    }
}