<?php

namespace App\Service;

use App\Tools\SqlTool;
use App\Tools\TokenTool;
use Illuminate\Support\Facades\DB;

class UserService extends BaseService
{



    /**
     * 生成房管局账号
     * @param $userInfo
     * @return mixed
     */
    public function generateFgjUser($userInfo)
    {
        $userInfo['id'] = SqlTool::makeUUID();
        $password = SqlTool::generatePassword(8);
        $userInfo['password'] = md5($password);
        DB::table('t_fgj')->insert($userInfo);
        return [
            'id' => $userInfo['id'],
            'login_name' => $userInfo['login_name'],
            'password' => $password
        ];
    }

    /**
     * @param $login_name 账号
     * @param $type 账号类型
     * @return bool
     */
    public function isLoginNameExist(string $login_name, string $type)
    {

        $res = DB::table(TokenTool::$config[$type]['table_name'])
            ->where(TokenTool::$config[$type]['login_column_name'], '=', $login_name)
            ->first();
        return $res !== null;
    }
    public function isPasswordSimple($password){
        if (123456 == $password){
            return true;
        }else{
            return false;
        }
    }

    public function getUserInfo($user_id,$userType){
           $user=DB::table(TokenTool::$config[$userType]['table_name'])->where('id','=',$user_id)
               ->select(TokenTool::$config[$userType]['select'])
               ->first();
           return $user;
   }

    public function getFgjXzqh($user_id)
    {
        $xzqh=DB::table('t_fgj')->where('id','=',$user_id)
            ->select('jc','sxzqh')
            ->first();
        return $xzqh;
    }

    public function login($login_name,string $type)
    {
        $userId=$this->getUserIdByLoginName($login_name,$type);
        $token = TokenTool::makeToken($userId,$type);
        return $token;
    }

    public function getUserIdByLoginName($login_name,$type){
        $userId=DB::table(TokenTool::$config[$type]['table_name'])
            ->where(TokenTool::$config[$type]['login_column_name'],'=',$login_name)
            ->value('id');
        return $userId;
    }

    public function isPasswordRight($login_name, $password,string $type)
    {
        $truePassword = DB::table(TokenTool::$config[$type]['table_name'])
            ->where(TokenTool::$config[$type]['login_column_name'],'=', $login_name)
            ->value(TokenTool::$config[$type]['password_column_name']);
        return $truePassword == md5($password);
    }

    public function resetPassword($id,$oldpassword,$password,$type){
        $truePassword = DB::table(TokenTool::$config[$type]['table_name'])
            ->where('id','=', $id)
            ->value(TokenTool::$config[$type]['password_column_name']);
        if ($truePassword == md5($oldpassword)){
            $status = DB::table(TokenTool::$config[$type]['table_name'])
                ->where('id',$id)
                ->update([TokenTool::$config[$type]['password_column_name']=>md5($password)]);
            if($status == 1){
                return 1000;
            }else{
                return 1001;
            }
        }else{
            return 1002;
        }

    }
}