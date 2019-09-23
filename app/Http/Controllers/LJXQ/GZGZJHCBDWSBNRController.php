<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/11/11
 * Time: 21:25
 */

namespace App\Http\Controllers\LJXQ;


use App\Http\Controllers\Controller;
use App\Service\LJXQ\GZGZJHCBDWSBNRService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Tools\ValidationHelper;

class GZGZJHCBDWSBNRController extends Controller
{
    private $gzgzjhcbdwsbnr;
    public function __construct(GZGZJHCBDWSBNRService $gzgzjhcbdwsbnr)
    {
        $this->gzgzjhcbdwsbnr = $gzgzjhcbdwsbnr;
    }
    public function create(Request $request){
        $rules = [
            'ssbid'=>'required|max:50',
            'ssbnr'=>'required|max:2000'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        $status = $this->gzgzjhcbdwsbnr->checkPower($userInfo['ssbid'],$request->user->sxzqh);
        if ($status == -1){
            return RequestTool::response(null,1002,'该单位没有权限操作');
        }
        $userInfo['id'] = SqlTool::makeUUID();
        $userInfo['ssbid'] = $request->ssbid;
        $userInfo['dsbrq'] = Carbon::now();
        $userInfo['sstatus'] = '暂存';
        $this->gzgzjhcbdwsbnr->create($userInfo);
        return RequestTool::response(null,1000,'添加上报内容成功');
    }
    public function update(Request $request){
        $rules = [
            'ssbnr'=>'required|max:2000'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        $rwid = $this->gzgzjhcbdwsbnr->getById($request->id)->ssbid;
        $status = $this->gzgzjhcbdwsbnr->checkPower($rwid,$request->user->sxzqh);
        if ($status == -1){
            return RequestTool::response(null,1002,'该单位没有权限操作');
        }
        $this->gzgzjhcbdwsbnr->update($request->id,$userInfo);
        return RequestTool::response(null,1000,'修改上报内容成功');
    }
    public function delete(Request $request){
        $rwid = $this->gzgzjhcbdwsbnr->getById($request->id)->ssbid;
        $status = $this->gzgzjhcbdwsbnr->checkPower($rwid,$request->user->sxzqh);
        if ($status == -1){
            return RequestTool::response(null,1002,'该单位没有权限操作');
        }
        $status = $this->gzgzjhcbdwsbnr->checkDelete($request->id);
        if ($status == -1){
            return RequestTool::response(null,1001,'已提交不能删除');
        }
        $this->gzgzjhcbdwsbnr->delete($request->id);
        return RequestTool::response(null,1000,'删除成功');
    }
    public function submmit(Request $request){
        $rwid = $this->gzgzjhcbdwsbnr->getById($request->id)->ssbid;
        $status = $this->gzgzjhcbdwsbnr->checkPower($rwid,$request->user->sxzqh);
        if ($status == -1){
            return RequestTool::response(null,1002,'该单位没有权限操作');
        }
        $code = $this->gzgzjhcbdwsbnr->submmit($request->id);
        if ($code == 0){
            return RequestTool::response(null,1000,'提交成功');
        }else{
            return RequestTool::response(null,1001,'请勿重复提交');
        }
    }
    public function search(Request $request){
        $rules = [
            'srwid'=>'required|max:50'
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        $status = $this->gzgzjhcbdwsbnr->checkPower($userInfo['srwid'],$request->user->sxzqh);
        if ($status == -1){
            return RequestTool::response(null,1002,'该单位没有权限操作');
        }
        $data = $this->gzgzjhcbdwsbnr->search($userInfo,$request);
        return RequestTool::response($data,1000,'查询任务列表成功');
    }
    public function detail(Request $request){
        $data = $this->gzgzjhcbdwsbnr->getById($request->id);
        return RequestTool::response($data,1000,'查询任务详情成功');
    }
}