<?php

namespace App\Http\Controllers\JSDW;

use App\Service\JSDW\JsdwJbxxService;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JSDWJBXXController extends Controller
{
    //
    private $jsdwService;
    private $rules = [
        'sqymc'=> 'required',
        'sbgdz'=> 'required',
        'nbgdzjd'=> 'required',
        'nbgdzwd'=> 'required',
        'slxr'=> 'required',
        'slxdh_gh'=> 'required',
        'slxdh_sj'=> 'required',
        'sxzqh'=> 'required',
        'dclrq'=> 'required',
        'iyyqx'=> 'required',
        'sfddbr'=> 'required',
        'sfrsfzjhm'=> 'required',
        'sfrlxdh'=> 'required',
        'sdjzclx'=> 'required',
        'sjyfw'=> 'required',
        'nzczj'=> 'required',
        'sdzyx'=> 'required',
        'sqywz'=> 'required',
        'syxbz'=> 'required',
        'sstatus'=> 'required',
        'stxr'=> 'required',
        'dtxrq'=> 'required',
    ];

    public function __construct(JsdwJbxxService $jsdwJbxxService)
    {
        $this->jsdwService = $jsdwJbxxService;
    }

    public function getByToken(Request $request)
    {
        $jsdw = $request->user;
        $jbxx = $this->jsdwService->showById($jsdw->id);
        unset($jbxx->dlmm);
        return response()->json([
            'code' => 1000,
            'message' => '根据token获取建设单位基本信息成功',
            'data' => $jbxx
        ]);
    }

    //保存
    public function update(Request $request)
    {
        $rules = [
            'sqymc'=> 'required',
            'sbgdz'=> 'required',
            'nbgdzjd'=> 'required',
            'nbgdzwd'=> 'required',
            'slxr'=> 'required',
            'slxdh_gh'=> 'required',
            'slxdh_sj'=> 'required',
            'sxzqh'=> 'required',
            'dclrq'=> 'required',
            'iyyqx'=> 'required',
            'sfddbr'=> 'required',
            'sfrsfzjhm'=> 'required',
            'sfrlxdh'=> 'required',
            'sdjzclx'=> 'required',
            'sjyfw'=> 'required',
            'nzczj'=> 'required',
            'sdzyx'=> 'required',
            'sqywz'=> 'required',
            'syxbz'=> 'required',
//            'sstatus'=> 'required',
//            'stxr'=> 'required',
//            'dtxrq'=> 'required',
        ];
        $res = ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $data = ValidationHelper::getInputData($request,$rules);
        $this->jsdwService->update($request->user->id,$data);
        return response()->json([
            'code'=>1000,
            'message' =>'保存成功'
        ]);
    }
}
