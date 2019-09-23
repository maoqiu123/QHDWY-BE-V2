<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/11/8
 * Time: 19:46
 */

namespace App\Http\Controllers\LJXQ;


use App\Http\Controllers\Controller;
use App\Http\Controllers\WYQY\CommonController;
use App\Service\LJXQ\GZGZJHCBDWService;
use App\Service\LJXQ\GZGZJHRWService;
use App\Tools\DtcxTool;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class GZGZJHRWController extends Controller
{
    private $gzgzjhrw;
    private $gzgzjhcbdw;
    private $commonController;
    public function __construct(GZGZJHRWService $gzgzjhrw,GZGZJHCBDWService $gzgzjhcbdw,CommonController $commonController)
    {
        $this->gzgzjhrw = $gzgzjhrw;
        $this->gzgzjhcbdw = $gzgzjhcbdw;
        $this->commonController = $commonController;
    }
    public function select(Request $request){
        $rules = [
            'sxzqh'=>'required|max:200'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        $scbdwid = DtcxTool::getBmByMc($userInfo['sxzqh']);
        $data['id'] = SqlTool::makeUUID();
        $data['srwid'] = $request->id;
        $data['scbdwid'] = $scbdwid;
        $status = $this->gzgzjhrw->isSelectExist($request->id,$scbdwid);
        if ($status != 0){
            return RequestTool::response(null,1002,'请勿重复选择');
        }
        $this->gzgzjhcbdw->create($data);
        return RequestTool::response(null,1000,'选择单位成功');
    }
    public function create(Request $request){
        $rules = [
            'srwbt'=>'required|max:50',
            'srwms'=>'required|max:200',
            'sjdyq'=>'required|max:2000',
            'dwcrq'=>'required|date',
            'dfbrq'=>'required|date',
            'sfbdw'=>'required|max:200'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        $userInfo['id'] = SqlTool::makeUUID();
        $userInfo['sbjbz'] = "未办结";
        $userInfo['sstatus'] = "暂存";
        $this->gzgzjhrw->create($userInfo);
        return RequestTool::response(null,1000,'下发任务成功');
    }

    public function update(Request $request){
        $rules = [
            'srwbt'=>'required|max:200',
            'srwms'=>'required|max:2000',
            'sjdyq'=>'required|max:2000',
            'dwcrq'=>'required|date',
            'dfbrq'=>'required|date',
            'sfbdw'=>'required|max:200'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        $this->gzgzjhrw->update($request->id,$userInfo);
        return RequestTool::response(null,1000,'修改下发任务成功');
    }
    public function search(Request $request){
        $data = $this->gzgzjhrw->search($request);
        return RequestTool::response($data,1000,'查询任务列表成功');
    }
    public function detail(Request $request){
        $data = $this->gzgzjhrw->getById($request->id);
        return RequestTool::response($data,1000,'查询任务详情成功');
    }
    public function submmit(Request $request){
        $code = $this->gzgzjhrw->submmit($request->id);
        if ($code == 0){
            return RequestTool::response(null,1000,'提交成功');
        }elseif ($code == -1){
            return RequestTool::response(null,1001,'请勿重复提交');
        }else{
            return RequestTool::response(null,1002,'已撤回，不允许提交');
        }
    }
    public function callback(Request $request){
        $code = $this->gzgzjhrw->callback($request->id);
        if ($code == 0){
            return RequestTool::response(null,1000,'撤回成功');
        }else{
            return RequestTool::response(null,1001,'请勿重复撤回');
        }
    }
    public function delete(Request $request){
        $status = $this->gzgzjhrw->checkDelete($request->id);
        if ($status == -1){
            return RequestTool::response(null,1001,'已提交不能删除');
        }elseif ($status == -2){
            return RequestTool::response(null,1000,'已有上报信息，不能删除');
        }
        $this->gzgzjhrw->delete($request->id);
        $this->gzgzjhrw->deleteCbdw($request->id);
        return RequestTool::response(null,1000,'删除成功');
    }
    public function finish(Request $request){
        $code = $this->gzgzjhrw->finish($request->id);
        if ($code == 0){
            return RequestTool::response(null,1000,'办结成功');
        }else{
            return RequestTool::response(null,1001,'请勿重复办结');
        }
    }
//    public function uploadFile($id,Request $request)
//    {
//        // dd($request->file);
//        $res = $this->commonController->uploadFile($id,'t_ljxq_sbrw','sbrw',$request->file);
//        if(!$res['success'])
//            return RequestTool::response($res,1101,"上传失败!");
//        return RequestTool::response($res,1000,"上传成功!");
//    }
}