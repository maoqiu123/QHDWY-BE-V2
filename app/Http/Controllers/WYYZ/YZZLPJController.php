<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/11/19
 * Time: 20:36
 */

namespace App\Http\Controllers\WYYZ;


use App\Http\Controllers\Controller;
use App\Service\WYYZ\YZZLPJService;
use Illuminate\Http\Request;
use App\Tools\ValidationHelper;
use App\Tools\RequestTool;

class YZZLPJController extends Controller
{
    private $yzzlpj;
    public function __construct(YZZLPJService $yzzlpj)
    {
        $this->yzzlpj = $yzzlpj;
    }
    public function create(Request $request){
        $rules=[
            'sbt'=>'required|max:200',
            'snrgs'=>'required|max:2000',
            'dfbrq'=>'required|date',
            'djzrq'=>'required|date',
            'sfbdw'=>'required|max:200',
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return RequestTool::response(null,1001,$res->errors());
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        $userInfo['id'] = SqlTool::makeUUID();
    }

}