<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/6/23
 * Time: 下午5:51
 */

namespace App\Http\Controllers\GKGS;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WYQY\CommonController;
use App\Service\FileService;
use App\Service\GKGS\GGFTYSYDGSService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class GGFTYSYDGSController extends Controller
{
    private $ggftysydgsService;
    private $fileService;
    private $commonController;
    public function __construct(CommonController $commonController,FileService $fileService,GGFTYSYDGSService $GGFTYSYDGSService)
    {
        $this->ggftysydgsService=$GGFTYSYDGSService;
        $this->fileService = $fileService;
        $this->commonController = $commonController;
    }

    /**
     * 根据项目名称和企业名称进行模糊匹配，并筛选出在公示日期区间内的公共分摊用水用电公示
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchGgftysydgs(Request $request)
    {
        $entry_name=$request->entry_name;
        $enterpriseName = $request->enterprise_name;
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $size = $request->size ?? 10;

        $ggftysydgs = $this->ggftysydgsService->searchGgftysydgs($entry_name , $enterpriseName, $publicity_begin, $publicity_end,$size);

        return RequestTool::response($ggftysydgs, 1000, '公共分摊用水用电公示查询成功');
    }
    /** 物业项目端 */
    private $ggsdft =[
        'sgsbt'=>'required|max:200',
        'dzq_q'=>'required|date_format:"Y-m-d"',
        'dzq_z'=>'required|date_format:"Y-m-d"',
        'sgsnr'=>'required',
        'dgsrq'=>'required|date_format:"Y-m-d"',
        'sbz'=>'max:400'
    ];

    public function createGgftysydgs(Request $request)
    {
        $rules = $this->ggsdft;
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $data = array_merge($data,[
            'id'=>SqlTool::makeUUID(),
            'xmid'=>$request->user->id,
            'qyid'=>$this->ggftysydgsService->getqyid($request->user->id),
            'dtxrq'=>Carbon::now(),
            'stxr'=>$request->user->id
        ]);
        $this->ggftysydgsService->create($data);
        return RequestTool::response(null, 1000, '添加成功');
    }

    public function updateGgftysydgs($id,Request $request)
    {
        $rules=$this->ggsdft;
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $sstatus=$this->ggftysydgsService->getstatus($id);
        //dd($sstatus);
        if($sstatus=='提交')
            return RequestTool::response(null,1001,'状态无法修改');
        $data = array_merge($data,[
            'dtxrq'=>Carbon::now(),
            'stxr'=>$request->user->id
        ]);
        $data['sstatus']='提交';
        $this->ggftysydgsService->update($id,$data);
        return RequestTool::response(null, 1000, '提交成功');
    }

    public function zanGgftysydgs($id,Request $request)
    {
        $rules=$this->ggsdft;
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
            return RequestTool::response($validator->errors(),1001,'表单验证失败');
        $data = ValidationHelper::getInputData($request,$rules);
        $sstatus=$this->ggftysydgsService->getstatus($id);
        //dd($data);
        if($sstatus=='提交')
            return RequestTool::response(null,1001,'状态无法修改');
        $data = array_merge($data,[
            'dtxrq'=>Carbon::now(),
            'stxr'=>$request->user->id
        ]);
        $this->ggftysydgsService->update($id,$data);
        return RequestTool::response($data, 1000, '暂存成功');
    }

    public function uploadFile($id,Request $request)
    {
        $res = $this->commonController->uploadFile($id,'t_xm_gs_ggftysydgs_zb','ggftysydgs',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_xm_gs_ggftysydgs_zb');
        return response()->json($res);
    }


    public function searchGgftysydgsForWy(Request $request){
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $ggftysydgs = $this->ggftysydgsService->searchGgftysydgsForWy($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($ggftysydgs, 1000, '公共分摊用水用电公示查询成功');
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

    public function deleteGgftysydgs($id)
    {
        $flag=$this->ggftysydgsService->getstatus($id);
        if($flag=='暂存') {
            $this->ggftysydgsService->delete($id);
            return RequestTool::response(null, 1000, '删除成功');
        }
        else {
            return RequestTool::response(null,1001,'状态为提交的不可删除');
        }
    }

    /** 业主端 */
    private $ggftysydgsYz =[
        'dgsrq'=>'required|date_format:"Y-m-d"',
        'dzq_q'=>'required|date_format:"Y-m-d"',
        'dzq_z'=>'required|date_format:"Y-m-d"',
        'sgsbt'=>'required|max:200',
        'sgsnr'=>'required',
        'sbz'=>'required|max:400'
    ];
    public function searchGgftysydgsForYz(Request $request){
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $ggftysydgs = $this->ggftysydgsService->searchGgftysydgsForYz($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($ggftysydgs, 1000, '公共分摊用水用电公示查询成功');
    }
    public function showDetail($ggftysydgsId,Request $request){
        $rules = $this->ggftysydgsYz;
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
        $htlxqk = $this->ggftysydgsService->showDetail($id,$ggftysydgsId,$data);
        if ($htlxqk == 1000){
            return RequestTool::response(null, 1000, '公共分摊用水用电公示修改成功');
        }else{
            return RequestTool::response(null, 1001, '公共分摊用水用电公示修改失败，请检查id是否正确或者是否重复修改');
        }
    }

    /** 业委会 */
    public function searchForYwh(Request $request){
        $publicity_begin=$request->publicity_begin;
        $publicity_end= $request->publicity_end;
        $id = $request->user->id;
        $size = $request->size ?? 10;

        $ggftysydgs = $this->ggftysydgsService->searchForYwh($id,$publicity_begin, $publicity_end,$size);

        return RequestTool::response($ggftysydgs, 1000, '公共分摊用水用电公示查询成功');
    }
}