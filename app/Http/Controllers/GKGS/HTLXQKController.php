<?php

namespace App\Http\Controllers\GKGS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WYQY\CommonController;
use App\Service\GKGS\HTLXQKService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Service\FileService;

class HTLXQKController extends Controller
{
    private $htlxqkService;
    private $fileService;
    private $commonController;
    public function __construct(CommonController $commonController,HTLXQKService $HTLXQKService,FileService $fileService)
    {
        $this->htlxqkService=$HTLXQKService;
        $this->fileService = $fileService;
        $this->commonController = $commonController;
    }

    /**
     * 根据项目名称和企业名称进行模糊匹配，并筛选出在公示日期区间内的合同履行情况公示
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchHtlxqk(Request $request)
    {

        $entry_name=$request->entry_name;
        $enterpriseName = $request->enterprise_name;
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $size = $request->size ?? 10;

        $htlxqk = $this->htlxqkService->searchHtlxqk($entry_name , $enterpriseName, $publicity_begin, $publicity_end,$size);

        return RequestTool::response($htlxqk, 1000, '合同履行情况公示查询成功');
    }

    /** 物业项目端 */
    private $htlxqk =[
        'htid'=>'required',
        'dzq_q'=>'required|date_format:"Y-m-d"',
        'dzq_z'=>'required|date_format:"Y-m-d"',
        'tgsbt'=>'required|max:200',
        'dgsrq'=>'required|date_format:"Y-m-d"',
        'sgsnr' => 'required'
    ];

    public function getHtlxqkListByXmId($xmid,Request $request){
        $list=$this->htlxqkService->getHtListByXmId($xmid);
        return response()->json([
            'code' => 1000,
            'message' => '获取项目合同列表成功',
            'data'=> $list
        ]);
    }


    public function createHtlxqk(Request $request)
    {
        $rules = $this->htlxqk;
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);

        $data = array_merge($data,[
           'id'=>SqlTool::makeUUID(),
           'dtxrq'=>Carbon::now(),
           'stxr'=>$request->user->id,
           'sstatus'=>'暂存'
        ]);
        $this->htlxqkService->create($data);
        return RequestTool::response(null, 1000, '添加成功');
    }

    public function updateHtlxqk($id,Request $request)
    {
        $rules =[
            'dzq_q'=>'required|date_format:"Y-m-d"',
            'dzq_z'=>'required|date_format:"Y-m-d"',
            'tgsbt'=>'required|max:200',
            'dgsrq'=>'required|date_format:"Y-m-d"',
            'sgsnr' => ''
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $sstatus=$this->htlxqkService->getstatus($id);
        //dd($sstatus);
        if($sstatus=='提交')
            return RequestTool::response(null,1001,'状态无法修改');
        $data = array_merge($data,[
            'dtxrq'=>Carbon::now(),
            'stxr'=>$request->user->id
        ]);
        $data['sstatus']='提交';
        $this->htlxqkService->update($id,$data);
        return RequestTool::response(null, 1000, '提交成功');
    }

    public function zanHtlxqk($id,Request $request)
    {
        $rules =[
            'dzq_q'=>'required|date_format:"Y-m-d"',
            'dzq_z'=>'required|date_format:"Y-m-d"',
            'tgsbt'=>'required|max:200',
            'dgsrq'=>'required|date_format:"Y-m-d"',
            'sgsnr' => 'required'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $sstatus=$this->htlxqkService->getstatus($id);
        //dd($data);
        if($sstatus=='提交')
            return RequestTool::response(null,1001,'状态无法修改');
        $data = array_merge($data,[
            'dtxrq'=>Carbon::now(),
            'stxr'=>$request->user->id
        ]);
        $this->htlxqkService->update($id,$data);
        return RequestTool::response(null, 1000, '暂存成功');
    }

    public function showHtlxqk($id,Request $request)
    {
        $data=$this->htlxqkService->show([
            ['id','=',$id]
        ]);
        return RequestTool::response($data,1000,'获取成功');
    }

    public function uploadFile($id,Request $request)
    {
        $res = $this->commonController->uploadFile($id,'t_xm_gs_htlxqk_zb','htlxqk',$request->file);

        if(!$res['success'])
            return RequestTool::response($res,1101,"上传失败!");

        return RequestTool::response($res,1000,"上传成功!");
    }

    public function deleteFile(Request $request)
    {
        if(!isset($request->fileid)||!isset($request->rowid))
            return RequestTool::response(null,1001,"未填写记录id或文件id!");
        $fileId = $request->fileid;
        $rowId= $request->rowid;
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_xm_gs_htlxqk_zb');
        return response()->json($res);
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
    public function searchHtlxqkForWy(Request $request)
    {
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $htlxqk = $this->htlxqkService->searchHtlxqkForWy($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($htlxqk, 1000, '合同履行情况公示查询成功');
    }
    public function searchHtForWy(Request $request)
    {
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $htlxqk = $this->htlxqkService->searchHtForWy($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($htlxqk, 1000, '合同查询成功');
    }

    public function delete($id)
    {
        $flag=$this->htlxqkService->getstatus($id);
        if($flag=='暂存') {
            $this->htlxqkService->delete($id);
            return RequestTool::response(null, 1000, '删除成功');
        }
        else {
            return RequestTool::response(null,1001,'状态为提交的不可删除');
        }
    }
    /**
     * 业主端
     */
    private $htlxqkYz =[
        'shtmc'=>'required',
        'dgsrq'=>'required|date_format:"Y-m-d"',
        'dzq_q'=>'required|date_format:"Y-m-d"',
        'dzq_z'=>'required|date_format:"Y-m-d"',
        'tgsbt'=>'required|max:200',
        'sgsnr'=>'required',
    ];
    public function searchHtlxqkForYz(Request $request)
    {
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $htlxqk = $this->htlxqkService->searchHtlxqkForYz($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($htlxqk, 1000, '合同履行情况公示查询成功');
    }

    /**
     * 点击查看详情的修改接口
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function showDetail($thlxqkId,Request $request){
        $rules = $this->htlxqkYz;
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
        $htlxqk = $this->htlxqkService->showDetail($id,$thlxqkId,$data);
        if ($htlxqk == 1000){
            return RequestTool::response(null, 1000, '合同履行情况公示修改成功');
        }else{
            return RequestTool::response(null, 1001, '合同履行情况公示修改失败，请检查id是否正确或者是否重复修改');
        }
    }
    /** 业委会 */
    public function searchHtlxqkForYwh(Request $request)
    {
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $htlxqk = $this->htlxqkService->searchHtlxqkForYwh($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($htlxqk, 1000, '合同履行情况公示查询成功');
    }
}