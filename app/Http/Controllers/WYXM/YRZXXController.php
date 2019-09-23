<?php

namespace App\Http\Controllers\WYXM;

use App\Http\Controllers\Controller;
use App\Service\WYXM\YRZXXService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YRZXXController extends Controller
{

    private $YRZXXService;

    public function __construct(YRZXXService $YRZXXService)
    {
        $this->YRZXXService = $YRZXXService;
    }

    /**
     * 搜索已入住信息
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $sxmbh = $request->sxmbh;
        $irzqs = $request->irzqs;
        $dbqjgsjStart = $request->dbqjgsj_start;
        $dbqjgsjEnd = $request->dbqjgsj_end;
        $dbqrzsjStart = $request->dbqrzsj_start;
        $dbqrzsjEnd = $request->dbqrzsj_end;
        $size = $request->size ?? 20;

        $yzxx = $this->YRZXXService->search($sxmbh, $irzqs, $dbqjgsjStart, $dbqjgsjEnd, $dbqrzsjStart, $dbqrzsjEnd, $size);
        return RequestTool::response($yzxx, '1000', '查询成功');
    }

    /**
     * 获取物业项目入驻信息
     * @param Request $request
     * @param $xmId
     * @return JsonResponse
     */
    public function show($xmId,Request $request)
    {
        $yrzxx = $this->YRZXXService
            ->show(['xmid', '=', $xmId]);
        return RequestTool::response($yrzxx, '1000', '查询成功');
    }

    /** 删除指定id项目入住信息 */
    public function delete($id)
    {
        $this->YRZXXService->delete($id);
        return RequestTool::response($id,1000,'删除成功');
    }

//    /**
//     * 添加物业项目入驻信息
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function create(Request $request)
//    {
//        //TODO 从token获取项目id
//        $rules = [
//            'xmid' => 'required|max:50',
//            'sxmbh' => 'required|max:30',
//            'irzqs' => 'required|numeric',
//            'dbqjgsj' => 'required',
//            'dbqrzsj' => 'required',
//            'nrzgm' => 'required|numeric',
//            'ifwzs' => 'required|numeric',
//            'ifwts' => 'required|numeric',
//            'srzfw' => 'required|max:50',
//        ];
//
//        $validator = Validator::make($request->all(), $rules);
//        if ($validator->fails())
//            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
//        $this->YRZXXService->save($request->all());
//        return RequestTool::response(null, '1000', '保存成功');
//    }

    /**
     *  根据项目Id更新已入住信息
     */

    public function update($id,Request $request){
        $user=$request->user;
        $xmid=$user->id;
        $rules=[
            'irzqs'=>'required|int',
            'dbqjgsj'=>'required',
            'dbqrzsj'=>'required',
            'nrzgm'=>'required',
            'srzfw'=>'required',
            'ifwzs'=>'required',
            'ifwts'=>'required',
            'ihz'=>'',
            'igh'=>'',
            'igjg'=>'',
            'iqt'=>'',
            'ighlstcw'=>'',
            'idstcw'=>'',
            'idxtcw'=>'',
            'ilstcw'=>'',
            'idt'=>'',
            'isssb'=>'',
            'idrsb'=>'',
            'ijkxt'=>'',
            'izsb'=>'',
            'ixfsb'=>'',
            'izysb'=>'',
            'izlsb'=>'',
            'sywhg'=>'',
            'nhgmj'=>'',
            'sywyyc'=>'',
            'sywwqc'=>'',
            'sywlqc'=>'',
            'sywksdj'=>'',
            'sywsxt'=>'',
            'sywyhw'=>'',
            'sywzzbj'=>'',
            'sywszxh'=>'',
            'ixfs'=>'',
            'ixfsx'=>'',
            'imhq'=>'',
            'sywyg'=>'',
            'sywpl'=>'',
            'sqt'=>'max:100',
            'sbz'=>'max:200'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $info=ValidationHelper::getInputData($request,$rules);
        $info=array_merge($info, [
            'sstatus'=>'提交',
            'stxr'=>$xmid,
            'dtxsj'=>Carbon::now()
        ]);
        $this->YRZXXService->updateINfo($id,$info);
        return RequestTool::response(null,1000,'更新成功');
    }

    /**
     *  根据项目Id创建已入住信息
     */

    public function create(Request $request){
        $user=$request->user;
        $xmid=$user->id;
        $rules=[
            'irzqs'=>'required|int',
            'dbqjgsj'=>'required',
            'dbqrzsj'=>'required',
            'nrzgm'=>'required',
            'srzfw'=>'required',
            'ifwzs'=>'required',
            'ifwts'=>'required',
            'ihz'=>'',
            'igh'=>'',
            'igjg'=>'',
            'iqt'=>'',
            'ighlstcw'=>'',
            'idstcw'=>'',
            'idxtcw'=>'',
            'ilstcw'=>'',
            'idt'=>'',
            'isssb'=>'',
            'idrsb'=>'',
            'ijkxt'=>'',
            'izsb'=>'',
            'ixfsb'=>'',
            'izysb'=>'',
            'izlsb'=>'',
            'sywhg'=>'',
            'nhgmj'=>'',
            'sywyyc'=>'',
            'sywwqc'=>'',
            'sywlqc'=>'',
            'sywksdj'=>'',
            'sywsxt'=>'',
            'sywyhw'=>'',
            'sywzzbj'=>'',
            'sywszxh'=>'',
            'ixfs'=>'',
            'ixfsx'=>'',
            'imhq'=>'',
            'sywyg'=>'',
            'sywpl'=>'',
            'sqt'=>'max:100',
            'sbz'=>'max:200'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $info=ValidationHelper::getInputData($request,$rules);
        $info=array_merge($info, [
            'xmid'=>$xmid,
            'id'=>SqlTool::makeUUID(),
            'sstatus'=>'暂存',
            'stxr'=>$xmid,
            'dtxsj'=>Carbon::now()
        ]);
        $this->YRZXXService->create($info);
        return RequestTool::response(null,1000,'保存成功');
    }

    /**
     * 根据项目Id获取已入住信息概况
     * @param $xmid
     * @param Request $request
     * @return JsonResponse
     */
    public function getTotalInfoByXmid($xmid,Request $request){
        $info = $this->YRZXXService->getTotalInfo($xmid);
        return response()->json([
            'code' =>1000,
            'message' => '根据项目Id获取已入住信息概况成功',
            'data'=> $info
        ]);
    }

    public function searchByXmid(Request $request){
        $user=$request->user;
        $xmid=$user->id;
        $size=$request->size ?? 10;
        $data=$this->YRZXXService->searchByXmid($xmid,$size);
        return RequestTool::response($data,1000,'查询成功');
    }

    public function searchById($id,Request $request){
        $data=$this->YRZXXService->showById($id);
        return RequestTool::response($data,1000,'查询成功');
    }
}