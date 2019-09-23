<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/16
 * Time: 下午4:27
 */

namespace App\Http\Controllers\WYQY;


use App\Http\Controllers\Controller;
use App\Service\WYQY\QYJBXXWHService;
use App\Tools\DtcxTool;
use App\Tools\RequestTool;
use App\Tools\SqlTool;
use App\Tools\ValidationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QYJBXXWHController extends Controller
{

    private $QYJBXXWHService;
    private $commonController;

    public function __construct(QYJBXXWHService $QYJBXXWHService,CommonController $commonController)
    {
        $this->QYJBXXWHService = $QYJBXXWHService;
        $this->commonController = $commonController;
    }


    public function uploadFile($id,Request $request)
    {
        // dd($request->file);
        $res = $this->commonController->uploadFile($id,'t_qyjbxx','qyjbxx',$request->file);
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
        $res = $this->commonController->deleteFile($fileId, $rowId,'t_qyjbxx');
        return response()->json($res);
    }

    public function searchEnterprise(Request $request)
    {
        $enterpriseCode = $request->enterprise_code;
        $enterpriseName = $request->enterprise_name;
        $regionId =  isset($request->region_id) ? SqlTool::getCodeBmBymc($request->region_id) : null ;
        $contacts = $request->contacts;
//        $sxzqh = $request->user->sxzqh;
//        $page = $request->page ?? 1;
        $size = $request->size ?? 20;


        $enterprises = $this->QYJBXXWHService->searchEnterprise(
            $enterpriseCode, $enterpriseName, $regionId, $contacts, '4*'.$regionId, $size);
        $used = [];
        foreach ($enterprises as $enterpris) {
            $used[] = $enterpris->sxzqh;
        }

        $xzqh = $this->QYJBXXWHService->getXzqh($used);
        foreach ($enterprises as $enterprise) {
            $enterprise->sxzqh = $xzqh[(string)$enterprise->sxzqh];
        }
        return RequestTool::response($enterprises, 1000, '');
    }

    public function initEnterprise(Request $request)
    {
        $rules = [
//            'sqybm' => 'required|max:50',
            'sshxydm' => 'required|max:50',
            'sqymc' => 'required|max:200',
//            'slxr' => 'required|max:50',
//            'slxdh_sj' => 'required|max:50',
            'sxzqh' => 'required|max:50',
//            'sxzqh' => 'required|max:20', // 行政区划自动填入
//            'syxbz' => 'required|max:10' // 有效标志默认为1
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);
        $isExist = $this->QYJBXXWHService->isColumnExist('sshxydm', $data['sshxydm']);
        if ($isExist)
            return RequestTool::response(null, 1001, '社会信用代码已存在');
        $data = array_merge($data, [
            'id' => SqlTool::makeUUID(),
            'sdlzh' => $data['sshxydm'],
            'sxzqh' => $this->QYJBXXWHService->getxzqhbm($request->sxzqh),
            'sdlmm' => md5('123456'),
            'ssfdlg' => '否',
            'syxbz' => '有效'
        ]);
        $this->QYJBXXWHService->create($data);
        return RequestTool::response(null, 1000, '添加成功');
    }

    public function deleteEnterprise(Request $request)
    {
        $Enterprises = $request->enterprise_ids;
        $results = [];
        $status=$this->QYJBXXWHService->getstatus($Enterprises)->sstatus;
        if($status=='提交')
            return RequestTool::response(null,1001,'已提交状态不允许删除');
        foreach ($Enterprises as $id) {
            $res = $this->QYJBXXWHService->deleteEnterprise($id);
            $results[] = [
                $id => ($res === true)?'删除成功':'该数据企业已经维护基本信息，不允许删除'
            ];
        }

        return RequestTool::response($results, 1000, '请求成功');
    }

    public function updateEnterpriseStatus(Request $request)
    {
        $rules = [
            'syxbz' => 'required' // 有效标志
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $flag = $request->input('syxbz');
        if($flag==1)
        $flag1='有效';
        else $flag1='无效';
        $ids = $request->enterprise_ids;
        $this->QYJBXXWHService->updates($ids, [
            'syxbz' => $flag1
        ]);
        return RequestTool::response(null, 1000, ($flag === 0) ? '作废成功' : '启用成功');
    }

    public function showEnterprise($id, Request $request)
    {
        $data = $this->QYJBXXWHService->show([
            ['id', '=', $id]
        ]);
        $data = $data->first();
        if ($data != null) {
            $data->sdlmm = '******';
        }
        $data->sxzqhmc = SqlTool::getXzqhMcByBm($data->sxzqh);
        return RequestTool::response($data, 1000, '查询成功');
    }

    public function initEnterpriseCode(Request $request)
    {
        $ids = $request->enterprise_ids;
        $this->QYJBXXWHService->updates($ids, [
            'sdlmm' => md5(123456),
            'ssfdlg' => '否'
        ]);
        return RequestTool::response(null, 1000, '初始化密码成功');
    }

    public function updateEnterpriseInfo($id, Request $request)
    {
        $rules = [
//        'sqybm' => 'required|max:50',
//        'sqymc' => 'required|max:200',
            'sbgdz' => 'required|max:200',
            'nbgdzjd' => 'required',
            'nbgdzwd' => 'required',
//        'slxr' => 'required|max:50',
            'slxdh_gh' => 'required|max:50',
//        'slxdh_sj' => 'required|max:50',
            'ssfkfjsdwps' => 'required|max:2',
            'sjsdwmc' => 'max:200',
//        'sxzqh' => 'required|max:20',
//        'sshxydm' => 'required|max:50',
//        'dclrq' => 'required|date_format:Y-m-d',
            'dyyqx' => 'required|date_format:Y-m-d',
            'sfddbr' => 'required|max:50',
            'sfrsfzjhm' => 'required|max:50',
            'sfrlxdh' => 'required|max:50',
            'sdjzclx' => 'required|max:10',
            'sjyfw' => 'required|max:2000',
            'nzczj' => '',
            'szzdj' => 'max:40',
            'szzzh' => 'max:50',
            'sdzyx' => 'max:100',
            'sqywz' => 'max:200',
            'ssfbsqy' => 'required|max:3',
            'dwbjqrq' => '',
            'ssfxhcydw' => 'required|max:3',
            'drhrq' => '',
//        'syxbz'=>'',
//        'stxr'=>'',
//        'dtxrq'=>'',
//        'ssfdlg'=>'',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);
        $data = array_merge($data, [
            'stxr' => $request->user->id,
            'dtxrq' => Carbon::now(),
            'sstatus'=>'提交',
            'sdlmm'=>'123'
        ]);
       // dd($data);
        $this->QYJBXXWHService->update($id, $data);
        return RequestTool::response(null, 1000, '更新成功');
    }

    private $qyjbxx = [
        'sqymc' => 'required|max:200',
        'sbgdz' => 'required|max:200',
        'nbgdzjd' => 'required',
        'nbgdzwd' => 'required',
        'slxr' => 'required|max:50',
        'slxdh_gh' => 'required|max:50',
        'slxdh_sj' => 'required|max:50',
        'ssfkfjsdwps' => 'required|max:2',
        'sjsdwmc' => 'max:200',
//        'sxzqh' => 'required|max:20',
        'dclrq' => 'required|date_format:Y-m-d',
        'dyyqx' => 'required|date_format:Y-m-d',
        'sfddbr' => 'required|max:50',
        'sfrsfzjhm' => 'required|max:50',
        'sfrlxdh' => 'required|max:50',
        'sdjzclx' => 'required|max:10',
        'sjyfw' => 'required|max:2000',
        'nzczj' => '',
        'szzdj' => 'max:40',
        'szzzh' => 'max:50',
        'sdzyx' => 'max:100',
        'sqywz' => 'max:200',
        'ssfbsqy' => 'required|max:3',
        'dwbjqrq' => '',
        'ssfxhcydw' => 'required|max:3',
        'drhrq' => '',
        'syxbz' => '',
        'sdlmm' => '',
        'ssfdlg' => '',
    ];

    public function createEnterpriseByQy(Request $request)
    {
        $rules = $this->qyjbxx;
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);
        $data = array_merge($data, [
            'id' => SqlTool::makeUUID(),
            'stxr' => $request->user->id,
            'dtxrq' => Carbon::now(),
        ]);
        $this->QYJBXXWHService->create($data);
        return RequestTool::response(null, 1000, '创建成功');
    }

    public function updateEnterpriseByQy($id, Request $request)
    {
        $rules = [
            'sqymc' => 'required|max:200',
            'sbgdz' => 'required|max:200',
            'nbgdzjd' => 'required',
            'nbgdzwd' => 'required',
            'slxr' => 'required|max:50',
            'slxdh_gh' => 'required|max:50',
            'slxdh_sj' => 'required|max:50',
            'ssfkfjsdwps' => 'required|max:2',
            'sjsdwmc' => 'max:200',
//        'sxzqh' => 'required|max:20',
            'dclrq' => 'required|date_format:Y-m-d',
            'dyyqx' => 'required|date_format:Y-m-d',
            'sfddbr' => 'required|max:50',
            'sfrsfzjhm' => 'required|max:50',
            'sfrlxdh' => 'required|max:50',
            'sdjzclx' => 'required|max:10',
            'sjyfw' => 'required|max:2000',
            'nzczj' => '',
            'szzdj' => 'max:40',
            'szzzh' => 'max:50',
            'sdzyx' => 'max:100',
            'sqywz' => 'max:200',
            'ssfbsqy' => 'required|max:3',
            'dwbjqrq' => '',
            'ssfxhcydw' => 'required|max:3',
            'drhrq' => ''
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return RequestTool::response($validator->errors(), 1001, '表单验证失败');
        $data = ValidationHelper::getInputData($request, $rules);
        $data = array_merge($data, [
            'stxr' => $request->user->id,
            'sstatus'=>'提交',
            'dtxrq' => Carbon::now(),
        ]);
        $this->QYJBXXWHService->update($id, $data);
        return RequestTool::response(null, 1000, '更新成功');
    }

    public function showEnterpriseByQy(int $id, Request $request)
    {
        $data = $this->QYJBXXWHService->showByQy($id);
        return RequestTool::response($data, 1000, '查询成功');
    }

    /** 业委会端 */
    public function searchQyjbxxForYwh(Request $request)
    {
        $ywhid=$request->user->id;
        $data=$this->QYJBXXWHService->getForYwh($ywhid);
        return RequestTool::response($data,1000,'查询成功');
    }

    public function searchXmjbxxForYwh(Request $request)
    {
        $ywhid=$request->user->id;
        $data=$this->QYJBXXWHService->getXmxxForYwh($ywhid);
        return RequestTool::response($data,1000,'查询成功');
    }
}