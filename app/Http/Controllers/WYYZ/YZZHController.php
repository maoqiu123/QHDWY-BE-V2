<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/10/18
 * Time: 21:07
 */

namespace App\Http\Controllers\WYYZ;

use App\Http\Controllers\Controller;
use App\Service\WYYZ\YZZHService;
use App\Tools\RequestTool;
use App\Tools\TokenTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class YZZHController extends Controller
{
    public $yzzhService;

    public function __construct(YZZHService $yzzhService)
    {
        $this->yzzhService=$yzzhService;
    }
    public function sendCode(Request $request){
        $rules=[
            'login_name'=>'required'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return RequestTool::response(null,1001,$res->errors());
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        $result = $this->yzzhService->sendCode($userInfo['login_name']);
        if ($result == true){
            return RequestTool::response(null,1000,'发送成功');
        }else{
            return RequestTool::response(null,1003,'发送失败，请稍后再试');
        }
    }

    /**
     * 创建业主账号
     */
    public function createAccountYz(Request $request){
        $rules=[
            'login_name'=>'required',
            'password'=>'required',
            'code'=>'required'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return RequestTool::response(null,1001,$res->errors());
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);

        if ($this->yzzhService->isPhoneExist($userInfo['login_name'],'yzzh')){
            return RequestTool::response(null,1020,'账号已存在');
        }
        $verifyResult = $this->yzzhService->verifyCode($userInfo['login_name'],$userInfo['code']);
        if ($verifyResult == 0){
            $user=$this->yzzhService->createAccountYz($userInfo);
            return RequestTool::response($user,1000,'注册成功');
        }elseif ($verifyResult == -1){
            return RequestTool::response(null,1001,'请先验证手机');
        }elseif ($verifyResult == -2){
            return RequestTool::response(null,1002,'验证码错误');
        }elseif ($verifyResult == -3){
            return RequestTool::response(null,1003,'验证码超时');
        }
    }

    public function login(Request $request){
        $rules=[
            'login_name'=>'required',
            'password'=> 'required',
            'login_type'=>'required'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return RequestTool::response(null,1001,$res->errors());
        }
        $login=ValidationHelper::getInputData($request,$rules);
        if (!$this->yzzhService->isPhoneExist($login['login_name'],'yzzh')){
            return RequestTool::response(null,1020,'账号不存在');
        }
        if (!$this->yzzhService->isPasswordRight($login['login_name'],$login['password'],$login['login_type'])){
            return RequestTool::response(null,1021,'密码错误');
        }
        $buildings = $this->yzzhService->showBuildings($login['login_name'],$login['login_type']);
        if (empty($buildings)){
            return RequestTool::response(null,1031,'该业主信息并无记录');
        }elseif (sizeof($buildings) == 1){
            $token =TokenTool::makeToken($buildings[0]->id,$login['login_type']);
            $userInfo=TokenTool::getUserByToken($token,'yz');
            $userInfo->sxmmc = $this->yzzhService->getXmmc($userInfo->xmid);
            $data = [
                'token'=>$token,
                'userInfo' => $userInfo
            ];
            return RequestTool::response($data,1000,'检测到你只有一栋楼房自动登陆中');
        }else{
            foreach ($buildings as $building){
                unset($building->sdlmm);
                unset($building->sdlzh);
            }
            $token = $this->yzzhService->makeYzzhToken($login['login_name']);
            $data['yzzh_token'] = $token;
            $data['building'] = $buildings;
            return RequestTool::response($data,1032,'请选择楼房');
        }
    }
    public function getTokenById(Request $request){
        $rules=[
            'id'=>'required'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return RequestTool::response(null,1001,$res->errors());
        }
        $binding=ValidationHelper::getInputData($request,$rules);
        $token =TokenTool::makeToken($binding['id'],'yzzh');
        $userInfo=TokenTool::getUserByToken($token,'yz');
        $data = [
            'token'=>$token,
            'userInfo' => $userInfo
        ];
        return RequestTool::response($data,1000,'登录成功');
    }
}