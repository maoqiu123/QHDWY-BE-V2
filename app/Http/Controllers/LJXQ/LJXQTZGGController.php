<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/11/15
 * Time: 19:50
 */

namespace App\Http\Controllers\LJXQ;


use App\Http\Controllers\Controller;
use App\Service\LJXQ\LJXQTZGGService;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use Illuminate\Http\Request;
use App\Tools\ValidationHelper;

class LJXQTZGGController extends Controller
{
    private $ljxqtzgg;
    public function __construct(LJXQTZGGService $ljxqtzgg)
    {
        $this->ljxqtzgg = $ljxqtzgg;
    }
    public function create(Request $request){
        $rules = [
            'sbt'=>'required|max:200',
            'snrgs'=>'required|max:2000',
            'dfbrq'=>'required|date',
            'sfbdw'=>'required|max:200',
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
        $userInfo['sstatus'] = '暂存';
        $this->ljxqtzgg->create($userInfo);
        return RequestTool::response(null,1000,'新增成功');
    }
    public function update(Request $request){
        $rules = [
            'sbt'=>'required|max:200',
            'snrgs'=>'required|max:2000',
            'dfbrq'=>'required|date',
            'sfbdw'=>'required|max:200',
        ];
        $res=ValidationHelper::validateCheck($request->input(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        $userInfo=ValidationHelper::getInputData($request,$rules);
        $this->ljxqtzgg->update($request->id,$userInfo);
        return RequestTool::response(null,1000,'修改成功');
    }
    public function submmit(Request $request){
        $code = $this->ljxqtzgg->submmit($request->id);
        if ($code == 0){
            return RequestTool::response(null,1000,'提交成功');
        }else{
            return RequestTool::response(null,1001,'请勿重复提交');
        }
    }
    public function delete(Request $request){
        $status = $this->ljxqtzgg->checkDelete($request->id);
        if ($status == -1){
            return RequestTool::response(null,1001,'已提交不能删除');
        }
        $this->ljxqtzgg->delete($request->id);
        return RequestTool::response(null,1000,'删除成功');
    }
    public function search(Request $request){
        $data = $this->ljxqtzgg->search($request);
        return RequestTool::response($data,1000,'查询任务列表成功');
    }
    public function detail(Request $request){
        $data = $this->ljxqtzgg->getById($request->id);
        return RequestTool::response($data,1000,'查询任务详情成功');
    }
}