<?php

namespace App\Http\Controllers;

use App\Service\UserService;
use App\Tools\TokenTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService=$userService;
    }

    public function generateFgj(Request $request){
        $rules=[
            'smc'=>'required',
            'sxzqh'=>'required',
            'jc' =>'required',
            'login_name'=>'required'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        if ($this->userService->isLoginNameExist($userInfo['login_name'],'fgj')){
            return response()->json([
                'code' => 1020,
                'message' => '账号已存在'
            ]);
        }
        $userInfo['status']=1;
        $user=$this->userService->generateFgjUser($userInfo);
        return response()->json([
            'code' => 1000,
            'message' => '注册成功',
            'data' =>$user
        ]);
    }

    public function login(Request $request){
        $rules=[
            'login_name'=>'required',
            'password'=> 'required',
            'login_type'=>'required'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $login=ValidationHelper::getInputData($request,$rules);
        if (!$this->userService->isLoginNameExist($login['login_name'],$login['login_type'])){
            return response()->json([
                'code' => 1019,
                'message' => '账号不存在'
            ]);
        }
        if (!$this->userService->isPasswordRight($login['login_name'],$login['password'],$login['login_type'])){
            return response()->json([
                'code' => 1021,
                'message' => '密码错误'
            ]);
        }
        $userId=$this->userService->getUserIdByLoginName($login['login_name'],$login['login_type']);
        $token =TokenTool::makeToken($userId,$login['login_type']);
        $userInfo=TokenTool::getUserByToken($token,$login['login_type']);
        if ($this->userService->isPasswordSimple($login['password'])){
            return response()->json([
                'code' => 1022,
                'message' => '密码简单，请修改密码',
                'data' => [
                    'token'=>$token,
                    'userInfo' => $userInfo
                ]
            ]);
        }
        return response()->json([
            'code' => 1000,
            'data' => [
                'token'=>$token,
                'userInfo' => $userInfo
            ]
        ]);
    }

    public function getUserInfo(Request $request){
        $rules=[
            'user_id'=> 'required',
            'user_type'=> 'required'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $data=ValidationHelper::getInputData($request,$rules);
        $user=$this->userService->getUserInfo($data['user_id'],$data['user_type']);
        return response()->json([
            'code'=> 1000,
            'message' => '获取用户信息成功',
            'data'=>$user
        ]);
    }

    public function isTokenExpired(Request $request)
    {
        return response()->json([
            'code'=> 1000,
            'message' => 'token未过期',
        ]);
    }
    public static $config=[
        'fgj'=>'t_fgj',
        'qy'=>'t_qyjbxx',
        'ywh'=>'t_ywh_jbxx',
        'xm'=>'t_xm_jbxx',
        'yz'=>'t_yz_yzjbxx',
        'jsdw'=>'t_jsdw_jbxx'
    ];
    public function resetPassword(Request $request){
        $type = $request->header('token_type');
        $status = $this->userService->resetPassword($request->user->id,$request->oldpassword,$request->password,$type);
        if ($status == 1000){
            return response()->json([
                'code'=> 1000,
                'message' => '密码修改成功',
            ]);
        }elseif ($status == 1001){
            return response()->json([
                'code'=> 1001,
                'message' => '密码修改失败，请稍后再试',
            ]);
        }else{
            return response()->json([
                'code'=> 1002,
                'message' => '密码修改失败，密码错误',
            ]);
        }
    }
}
