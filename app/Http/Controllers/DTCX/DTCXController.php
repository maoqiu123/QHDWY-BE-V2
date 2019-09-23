<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/20
 * Time: 上午11:11
 */

namespace App\Http\Controllers\DTCX;


use App\Http\Controllers\Controller;
use App\Service\GKGS\GGFTYSYDGSService;
use App\Service\GKGS\GGWXZJSYQKService;
use App\Service\GKGS\HTLXQKService;
use App\Service\GKGS\WTJYSZQKService;
use App\Service\LJXQ\LJXQJBXXService;
use App\Service\WYQY\QYJBXXWHService;
use App\Service\WYXM\WYXMJBXXService;
use App\Service\WYXM\YZBXQKService;
use App\Service\WYXM\YZTSXXService;
use App\Tools\DtcxTool;
use App\Tools\RequestTool;
use Illuminate\Http\Request;
use function PHPSTORM_META\type;

class DTCXController extends Controller
{
    private $QYJBXXWHService;
    private $WYXMJBXXService;
    private $LJXQService;
    private $HTLXQKService;
    private $WTJYSZQKService;
    private $GGFTYSYDGSService;
    private $GGWXZJSYQKService;
    private $YZTSXXService;
    private $YZBXXXService;
    public function __construct(
        YZTSXXService $YZTSXXService,
        QYJBXXWHService $QYJBXXWHService,
        WYXMJBXXService $WYXMJBXXService,
        LJXQJBXXService $LJXQJBXXService,
        HTLXQKService $HTLXQKService,
        WTJYSZQKService $WTJYSZQKService,
        GGFTYSYDGSService $GGFTYSYDGSService,
        GGWXZJSYQKService $GGWXZJSYQKService,
        YZBXQKService $YZBXQKService
    )
    {
        $this->YZTSXXService=$YZTSXXService;
        $this->YZBXXXService=$YZBXQKService;
        $this->QYJBXXWHService = $QYJBXXWHService;
        $this->WYXMJBXXService = $WYXMJBXXService;
        $this->LJXQService = $LJXQJBXXService;
        $this->HTLXQKService = $HTLXQKService;
        $this->WTJYSZQKService = $WTJYSZQKService;
        $this->GGFTYSYDGSService = $GGFTYSYDGSService;
        $this->GGWXZJSYQKService = $GGWXZJSYQKService;
    }

    /**
     * 根据企业Id与时间获取合同履行情
     * @param $qyid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHtlxqkByQyId($qyid, Request $request)
    {
        $month = $request->month ?? 1;
        $size  = $request->size ?? 20;
        $hts = $this->HTLXQKService->searchByQyId($qyid,$month,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据企业Id获取合同履行情况成功',
            'data' => $hts
        ]);
    }
    /**
     * 根据企业Id获取委托经营收支情况情况
     * @param $qyid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWtjyszqkByQyId($qyid, Request $request){
        $month = $request->month ?? 1;
        $size  = $request->size ?? 20;
        $hts = $this->WTJYSZQKService->searchByQyId($qyid,$month,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据企业Id获取委托经营收支情况情况成功',
            'data' => $hts
        ]);
    }

    /**
     * 根据企业Id查询公共水电费用分摊情况
     * @param $qyid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGgsdfyftByQyId($qyid,Request $request){
        $month = $request->month ?? 1;
        $size  = $request->size ?? 20;
        $hts = $this->GGFTYSYDGSService->searchByQyId($qyid,$month,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据企业Id查询公共水电费用分摊情况成功',
            'data' => $hts
        ]);
    }

    /**
     * 根据企业Id查询公共水电费用分摊情况
     * @param $qyid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWxjjsyqkByQyId($qyid, Request $request){
        $month = $request->month ?? 1;
        $size  = $request->size ?? 20;
        $hts = $this->GGWXZJSYQKService->searchByQyId($qyid,$month,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据企业Id查询公共水电费用分摊情况成功',
            'data' => $hts
        ]);
    }
    /**
     * 通过企业ID查询公开公示信息概述
     * @param $qyid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQygkgs($qyid, Request $request)
    {
        $htlxqk_num = $this->HTLXQKService->getQyHtlxqkNum($qyid);
        $wtjyszqk_num = $this->WTJYSZQKService->getQyWtjyszqkNum($qyid);
        $sdfyft_num = $this->GGFTYSYDGSService->getQySdfyftNum($qyid);
        $wxjjsyqk_num= $this->GGWXZJSYQKService->getQyWxjjNum($qyid);
        return response()->json([
            'code' => 1000,
            'message' => '成功',
            'data' => [
                'wyhtlxqk' => $htlxqk_num,
                'wtjyszqk' => $wtjyszqk_num,
                'ggsdfyft' => $sdfyft_num,
                'wxjjsyqk' => $wxjjsyqk_num
            ]
        ]);
    }

    /**
     * 根据企业Id查询投诉报修信息
     * @param $qyid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQytsbx($qyid, Request $request)
    {
        $tsxx = $this->YZTSXXService->getQytsxxNumSplitBySlzt($qyid);
        $bxxx= $this->YZBXXXService->getQyBxxxNumBySlzt($qyid);

        return response()->json([
            'code' => 1000,
            'message' => '成功',
            'data' => [
                'tsxxwsl' => $tsxx['wsl'],
                'tsxxwbj' => $tsxx['wbj'],
                'tsxxbmy' => $tsxx['bmy'],
                'bxxxwsl' => $bxxx['wsl'],
                'bxxxwbj' => $bxxx['wbj'],
                'bxxxbmy' => $bxxx['bmy'],
            ]
        ]);
    }

    /**
     * 通过企业名称进行模糊匹配查询，并返回经纬度列表与企业名称
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchEnterpriseByName(Request $request)
    {
        $enterpriseName = $request->enterprise_name;
        $xzqh = $request->xzqh ?? '2*1300000';
        $enterprise = $this->QYJBXXWHService->searchEnterprise(null, $enterpriseName, null, null, $xzqh, PHP_INT_MAX, ['id', 'sqymc', 'nbgdzjd', 'nbgdzwd'])->toArray();
//        $data = [
////            'center_point' => $this->calCenterPoint($enterprise),
//            'enterprises' => $enterprise->data,
//        ];
        $center = $this->calCenterPoint($enterprise,'qy');
        return RequestTool::response($enterprise['data'], 1000, '查询成功');
    }

    /**
     * 计算一组经纬度的地图中心点
     * @param $points
     * @param $type
     * @return array
     */
    private function calCenterPoint($points, string $type)
    {
        $minJd = 5000;
        $minWd = 5000;
        $maxJd = 0;
        $maxWd = 0;
        $jd = DtcxTool::$config[$type]['jd'];
        $wd = DtcxTool::$config[$type]['wd'];
        foreach ($points as $point) {
            if (isset($point->$jd)) {
                if ($point->$jd > $maxJd) {
                    $maxJd = $point->$jd;
                }

                if ($point->$jd < $minJd) {
                    $minJd = $point->$jd;
                }
            }

            if (isset($point->$wd)) {
                if ($point->$wd > $maxWd) {
                    $maxWd = $point->$wd;
                }


                if ($point->$wd < $minWd) {
                    $minWd = $point->$wd;
                }
            }

        }
        return [
            'jd' => ($minJd + $maxJd) / 2,
            'wd' => ($minWd + $maxWd) / 2,
            'jdkd' => ($maxJd - $minJd),
            'wdkd' => ($maxWd - $minWd),
        ];
    }

    /**
     * 通过行政区划筛选企业经纬度列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchEnterpriseByRegion(Request $request)
    {
        $regionId = DtcxTool::getXzqhTrueStr($request->region_id);
        $enterprise = $this->QYJBXXWHService->searchEnterprise(null, null, $regionId, null, PHP_INT_MAX, ['id', 'sqymc', 'nbgdzjd', 'nbgdzwd'])->toArray();
        $data = [
            'center_point' => $this->calCenterPoint($enterprise['data'], 'qy'),
            'enterprises' => $enterprise['data'],
        ];
        return RequestTool::response($data, 1000, '查询成功');
    }

//    public function showEnterpriseDetail(Request $request)
//    {
//        //组装企业信息，项目信息
//    }

    /**
     * 根据企业Id查询企业基本信息
     * @param $qyid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQyjbxx($qyid, Request $request)
    {

        $qyjbxx = $this->QYJBXXWHService->getQyxxByQyId($qyid);
        $qyjbxx->sxzqh = DtcxTool::getChineseXzqh($qyjbxx->sxzqh);
        // TODO 信用评级
        $qyjbxx->xypj = 5;
        return response()->json([
            'code' => 1000,
            'message' => '获取企业信息成功',
            'data' => $qyjbxx
        ]);
    }

    /**
     * 根据企业Id查询企业项目信息
     * @param $qyid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQyxms($qyid, Request $request)
    {
        $qyxms = $this->WYXMJBXXService->getXmsByQyId($qyid);
        $center = $this->calCenterPoint($qyxms, 'xm');
        return response()->json([
            'code' => 1000,
            'message' => '获取企业项目信息成功',
            'data' => [
                'qyxms' => $qyxms,
                'center' => $center
            ]
        ]);
    }


    /**
     * 根据企业Id查询投诉报修信息
     * @param $qyid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    /*****************************************************************************************************************************************************/
    /**
     * 根据项目Id与时间获取合同履行情
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHtlxqkByXmId($xmid, Request $request)
    {
        $month = $request->month ?? 1;
        $size  = $request->size ?? 20;
        $hts = $this->HTLXQKService->searchByXmId($xmid,$month,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据项目Id获取合同履行情况成功',
            'data' => $hts
        ]);
    }
    /**
     * 根据项目Id获取委托经营收支情况情况
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWtjyszqkByXmId($xmid, Request $request){
        $month = $request->month ?? 1;
        $size  = $request->size ?? 20;
        $hts = $this->WTJYSZQKService->searchByXmId($xmid,$month,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据项目Id获取委托经营收支情况情况成功',
            'data' => $hts
        ]);
    }

    /**
     * 根据项目Id查询公共水电费用分摊情况
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGgsdfyftByXmId($xmid,Request $request){
        $month = $request->month ?? 1;
        $size  = $request->size ?? 20;
        $hts = $this->GGFTYSYDGSService->searchByXmId($xmid,$month,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据项目Id获取公共水电费用分摊情况成功',
            'data' => $hts
        ]);
    }
    /**
     * 根据项目Id查询维修基金使用情况
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWxjjsyqkByXmId($xmid, Request $request){
        $month = $request->month ?? 1;
        $size  = $request->size ?? 20;
        $hts = $this->GGWXZJSYQKService->searchByXmId($xmid,$month,$size);
        return response()->json([
            'code' => 1000,
            'message' => '根据项目Id获取公共维修基金情况成功',
            'data' => $hts
        ]);
    }
    /**
     * 根据项目名称模糊匹配物业项目列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchXmByName(Request $request)
    {
        $xmmc = $request->sxmmc;
        $xzqh = $request->xzqh ?? '2*13000000';
        $xms = $this->WYXMJBXXService->search($xmmc, null, null, null, null, null, null, null, $xzqh, null,PHP_INT_MAX, ['t_xm_jbxx.id', 'sxmmc', 'njd', 'nwd'])->toArray();
        return RequestTool::response($xms['data'], 1000, '查询成功');
    }

    /**
     * 根据项目Id 获取项目基本信息
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getXmjbxx($xmid, Request $request)
    {
        $xmxx = $this->WYXMJBXXService->getXmByXmId($xmid);
        $xmxx->sssqx = DtcxTool::getChineseXzqh($xmxx->sssqx);
        return response()->json([
            'code' => 1000,
            'message' => '获取项目基本信息成功',
            'data' => $xmxx
        ]);
    }

    /**
     * 根据项目Id 获取服务该项目的物业企业的基本信息
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getXmqyxx($xmid, Request $request)
    {
        $qyxx = $this->WYXMJBXXService->getQyByXmId($xmid);
        $qyxx->sxzqh = DtcxTool::getChineseXzqh($qyxx->sxzqh);
        return response()->json([
            'code' => 1000,
            'message' => '获取项目企业信息成功',
            'data' => $qyxx
        ]);
    }

    /**
     * 根据项目Id 查询项目公开公示信息
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getXmgkgs($xmid, Request $request)
    {

        $htlxqk_num = $this->HTLXQKService->getXmHtlxqkNum($xmid);
        $wtjyszqk_num= $this->WTJYSZQKService->getXmWtjyszqkNum($xmid);
        $sdfyft_num = $this->GGFTYSYDGSService->getXmSdfyftNum($xmid);
        $wxjj_num = $this->GGWXZJSYQKService->getXmWxjjNum($xmid);
        return response()->json([
            'code' => 1000,
            'message' => '成功',
            'data' => [
                'wyhtlxqk' => $htlxqk_num,
                'wtjyszqk' => $wtjyszqk_num,
                'ggsdfyft' => $sdfyft_num,
                'wxjjsyqk' => $wxjj_num
            ]
        ]);
    }

    /**
     * 根据项目Id 查询项目投诉报修信息
     * @param $xmid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getXmtsbx($xmid, Request $request)
    {
        $tsxx = $this->YZTSXXService->getXmtsxxNumSplitBySlzt($xmid);
        $bxxx= $this->YZBXXXService->getXmtsxxNumSplitBySlzt($xmid);
//        dd($tsxx,$bxxx);
        return response()->json([
            'code' => 1000,
            'message' => '成功',
            'data' => [
                'tsxxwsl' => $tsxx['wsl'],
                'tsxxwbj' => $tsxx['wbj'],
                'tsxxbmy' => $tsxx['bmy'],
                'bxxxwsl' => $bxxx['wsl'],
                'bxxxwbj' => $bxxx['wbj'],
                'bxxxbmy' => $bxxx['bmy'],
            ]
        ]);
    }

    public function getXmByXzqh($xzqh, Request $request)
    {

    }
    /************************************************************************************************************************************************************/

    /**
     * 根据行政区划批量获取老旧小区列表
     * @param $xzqh
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLjxqByXzqh($xzqh, Request $request)
    {
        $xzqh = DtcxTool::getXzqhTrueStr($xzqh);
        $jwd = $this->LJXQService->getJWD($xzqh);
        $center = $this->calCenterPoint($jwd, 'ljxq');
        return response()->json([
            'code' => 1000,
            'data' => [
                'jwd' => $jwd,
                'center' => $center
            ]
        ]);
    }

    /**
     * 根据小区名称模糊匹配小区下拉列表
     * @param $name
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLjxqByName(Request $request)
    {
        $name = $request->name;
        $xzqh = $request->xzqh ?? '2*1300000';
        $list = $this->LJXQService->getJWDmh($name, $xzqh);
        return response()->json([
            'code' => 1000,
            'data' => $list
        ]);
    }

    /**
     * 根据小区Id获取小区详细信息
     * @param $ljxqid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLjxqById($ljxqid, Request $request)
    {
        $ljxq = $this->LJXQService->getLjxqById($ljxqid);
        return response()->json([
            'code' => 1000,
            'message' => '获取老旧小区详细信息成功',
            'data' => $ljxq
        ]);
    }

    /**
     * 根据小区Id获取小区相关的改造计划
     * @param $ljxqid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGzgzjhByLjxqid($ljxqid, Request $request)
    {
        $gzgzjh = $this->LJXQService->getGzgzjhById($ljxqid);
        return response()->json([
            'code' => 1000,
            'message' => '获取改造工作计划成功',
            'data' => $gzgzjh
        ]);
    }

    /**
     * 根据改造工作计划Id查看工作计划的进度
     * @param $gzgzjhid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGzgzjdById($gzgzjhid, Request $request)
    {
        $gzjd = $this->LJXQService->getGzgzjdById($gzgzjhid);
        return response()->json([
            'code' => 1000,
            'message' => '获取改造工作进度成功',
            'data' => $gzjd
        ]);
    }

}