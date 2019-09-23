<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\Controller;
use App\Service\WYXM\RYPZService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RYPZController extends Controller
{

    private $RYPZService;

    public function __construct(RYPZService $RYPZService)
    {
        $this->RYPZService = $RYPZService;
    }

    /**
     * 搜索人员配置
     * @param Request $request
     */
    public function search(Request $request)
    {
        $sxmbh = $request->sxmbh;
        $size = $request->size ?? 20;

        $yzxx = $this->RYPZService->search($sxmbh, $size);
        return RequestTool::response($yzxx, '1000', '查询成功');
    }

    /**
     * 获取物业项目入驻信息
     * @param Request $request
     */
    public function show($xmId)
    {
        $rypz = $this->RYPZService
            ->show(['xmid', '=', $xmId]);
        return RequestTool::response($rypz, '1000', '查询成功');
    }

//    /**
//     * 添加物业项目入驻信息
//     * @param Request $request
//     */
//    public function create(Request $request)
//    {
//        //TODO 从token获取项目id
//        $rules = [
//            'xmid' => 'required|max:50',
//            'sxmbh' => 'required|max:30',
//            'swyqymc' => 'required|max:100',
//            'djgxmsj' => 'required',
//            'slxdh' => 'required|max:30',
//            'swyqyyjsdwgx' => 'required|max:30',
//            'dhtqssj' => 'required',
//            'dhtzzsj' => 'required',
//            'ssfba' => 'required|max:2',
//            'dbasj' => 'required',
//            'dhtbah' => 'required|max:30',
//            'ixmzrs' => 'required|numeric',
//            v];
//
//        $validator = Validator::make($request->all(), $rules);
//        if ($validator->fails())
//            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
//        $this->RYPZService->save($request->all());
//        return RequestTool::response(null, '1000', '暂存成功');
//    }

    public function update(Request $request)
    {
        $user=$request->user;
        $xmid=$user->id;
        $rules=[
//            'swyqymc'=>'required|max:100',
//            'swyqyyjsdwgx'=>'required|max:30',
//            'djgxmsj'=>'required',
//            'slxdh'=>'required|max:30',
//            'dhtqssj'=>'required',
//            'dhtzzsj'=>'required',
//            'ssfba'=>'required|max:2',
//            'dbasj'=>'required|date_format:"Y-m-d"',
//            'dhtbah'=>'required|max:30',
//            'dhtyzrq'=>'',
//            'shtyqsm'=>'max:100',
            'ixmzrs'=>'required',
            'iglfwryzs'=>'required',
//            'iczryzs'=>'required',
            'ixmjl'=>'required',
//            'igly'=>'',
            'ijdry'=>'',
            'ibjy'=>'',
            'igcjsry'=>'',
            'izxwhry'=>'',
            'ilhry'=>'',
            'iqtry'=>'',
            'izjyry'=>'',
            'izygsryzs'=>'',
            'izygsgcjsry'=>'',
            'izygsbjry'=>'',
            'izygslhry'=>'',
            'izygsqtry'=>'',
            'izygszjyry'=>''
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $info=ValidationHelper::getInputData($request,$rules);
        $info=array_merge($info,[
                'sstatus'=>'暂存',
                'stxr'=>$xmid,
                'dtxsj'=>Carbon::now()
        ]);
        $this->RYPZService->update($xmid,$info);
        return RequestTool::response(null,1000,'更新成功');
    }

    public function searchForXm(Request $request)
    {
        $user=$request->user;
        $xmid=$user->id;
        $data=$this->RYPZService->searchByXmid($xmid)->first();
        return RequestTool::response($data,1000,'查询成功');
    }
    public function searchById($id,Request $request){
        $data=$this->RYPZService->showById($id);
        return $data;
    }
}