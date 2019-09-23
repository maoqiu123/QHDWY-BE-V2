<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午6:08
 */

namespace App\Http\Controllers\GKGS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WYQY\CommonController;
use App\Service\GKGS\GGWXZJSYQKService;
use App\Service\FileService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GGWXZJSYQKController extends Controller
{
    private $ggwxzjsyqkService;
    private $fileService;
    private $commonController;
    public function __construct(CommonController $commonController,GGWXZJSYQKService $GGWXZJSYQKService,FileService $fileService)
    {
        $this->ggwxzjsyqkService=$GGWXZJSYQKService;
        $this->fileService = $fileService;
        $this->commonController = $commonController;
    }

    /**
     * 根据项目名称和企业名称进行模糊匹配，并筛选出在公示日期区间内的公共维修资金使用公示
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchGgwxzjsyqk(Request $request)
    {
        $entry_name=$request->entry_name;
        $enterpriseName = $request->enterprise_name;
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $size = $request->size ?? 10;

        $ggwxzjsyqk = $this->ggwxzjsyqkService->searchGgwxzjsyqk($entry_name , $enterpriseName, $publicity_begin, $publicity_end,$size);

        return RequestTool::response($ggwxzjsyqk, 1000, '公共维修资金使用公示查询成功');
    }
    /** 物业项目端 */
    private $wxzjsy=[
        'swxxm'=>'required|max:200',
        'swxxmjs'=>'required|max:2000',
        'nysje'=>'required',
        'djhwxrq_q'=>'required|date_format:"Y-m-d"',
        'djhwxrq_z'=>'required|date_format:"Y-m-d"',
        'nzyzs'=>'required|integer',
        'nzyzsfwmj'=>'required',
        'ntyzyzs'=>'required|integer',
        'ntyzyzsfwmj'=>'required',
        'dgsrq'=>'required|date_format:"Y-m-d"'
    ];

    public function createGgwxzjsyqk(Request $request)
    {
        $rules=$this->wxzjsy;
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'id'=>SqlTool::makeUUID(),
            'xmid'=>$request->user->id,
            'qyid'=>$this->ggwxzjsyqkService->getqyid($request->user->id),
            'dtxrq'=>Carbon::now(),
            'stxr'=>$request->user->id
        ]);
        $this->ggwxzjsyqkService->create($data);
        return RequestTool::response(null, 1000, '添加成功');
    }

    public function updateGgwxzjsyqk($id,Request $request)
    {
        $rules=$this->wxzjsy;
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $sstatus=$this->ggwxzjsyqkService->getstatus($id);
        //dd($sstatus);
        if($sstatus=='提交')
            return RequestTool::response(null,1001,'状态无法修改');
        $data = array_merge($data,[
            'dtxrq'=>Carbon::now(),
            'stxr'=>$request->user->id
        ]);
        $data['sstatus']='提交';
        $this->ggwxzjsyqkService->update($id,$data);
        return RequestTool::response(null, 1000, '提交成功');
    }

    public function zanGgwxzjsyqk($id,Request $request)
    {
        $rules=$this->wxzjsy;
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $sstatus=$this->ggwxzjsyqkService->getstatus($id);
        //dd($data);
        if($sstatus=='提交')
            return RequestTool::response(null,1001,'状态无法修改');
        $data = array_merge($data,[
            'dtxrq'=>Carbon::now(),
            'stxr'=>$request->user->id
        ]);
        $this->ggwxzjsyqkService->update($id,$data);
        return RequestTool::response(null, 1000, '暂存成功');
    }
    public function searchWxzjsyqkForWy(Request $request){
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $ggwxzjsyqk = $this->ggwxzjsyqkService->searchWxzjsyqkForWy($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($ggwxzjsyqk, 1000, '公共维修资金使用公示查询成功');
    }

    public function downloadFile($id)
    {
        $file = $this->fileService->getDownloadFile($id);
        if($file == null)
            return RequestTool::response(null,1102,"文件不存在!");

        $filePath = $file->fjlj;
        $fileName = $file->fjmc;

        return response()->download($filePath,$fileName);
    }

    public function delete($id)
    {
        $flag=$this->ggwxzjsyqkService->getstatus($id);
        if($flag=='暂存') {
            $this->ggwxzjsyqkService->delete($id);
            return RequestTool::response(null, 1000, '删除成功');
        }
        else {
            return RequestTool::response(null,1001,'状态为提交的不可删除');
        }
    }

    /**
     * 业主端
     */
    private $ggwxzjsyqkYz =[
        'dgsrq'=>'required|date_format:"Y-m-d"',
        'djhwxrq_q'=>'required|date_format:"Y-m-d"',
        'djhwxrq_z'=>'required|date_format:"Y-m-d"',
        'swxxm'=>'required|max:200',
        'swxxmjs'=>'required|max:2000',
        'nysje'=>'required',
        'nzyzs'=>'required|integer',
        'ntyzyzs'=>'required|integer',
        'nzyzsfwmj'=>'required',
        'ntyzyzsfwmj'=>'required'
    ];
    public function searchWxzjsyqkForYz(Request $request){
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $ggwxzjsyqk = $this->ggwxzjsyqkService->searchWxzjsyqkForYz($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($ggwxzjsyqk, 1000, '公共维修资金使用公示查询成功');
    }
    public function showDetail($ggwxzjsyqkId,Request $request)
    {
        $rules = $this->ggwxzjsyqkYz;
        //进行验证，成功则返回数据数组，失败则返回JsonResponse类
        $data = ValidationHelper::checkAndGet($request, $rules, 1001);
        //如果data类型为response则return，否则继续
        $flag = 1;
        $className = null;
        try {
            $className = class_basename($data);
        } catch (\Exception $exception) {
            $flag = 0;
        }
        if ($flag == 1 && $className == 'JsonResponse')
            return $data;
        $id = $request->user->id;
        $htlxqk = $this->ggwxzjsyqkService->showDetail($id, $ggwxzjsyqkId, $data);
        if ($htlxqk == 1000) {
            return RequestTool::response(null, 1000, '公共维修资金使用公示修改成功');
        } else {
            return RequestTool::response(null, 1001, '公共维修资金使用公示修改失败，请检查id是否正确或者是否重复修改');
        }
    }

    /** 业委会端 */
    public function searchForYwh(Request $request){
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $ggwxzjsyqk = $this->ggwxzjsyqkService->searchForYwh($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($ggwxzjsyqk, 1000, '查询成功');
    }
}