<?php

namespace App\Tools;

use Illuminate\Support\Facades\DB;

class DtcxTool
{

    public static $config = [
        'qy' => [
            'jd' => 'nbgdzjd',
            'wd' => 'nbgdzwd'
        ],
        'xm' => [
            'jd' => 'njd',
            'wd' => 'nwd'
        ],
        'ljxq' => [
            'jd' => 'njd',
            'wd' => 'nwd'
        ]
    ];

    public static function getXzqhTrueStr($xzqhStr)
    {
        $xzqh=explode('*',$xzqhStr);
        $trueLength = [
            '2'=>2,
            '3'=>4,
            '4'=>6,
            '5'=>9,
            '6'=>12
        ];
        $trueStr=substr($xzqh[1],0,$trueLength[$xzqh[0]]);
        return $trueStr;
    }

    public static function getLastXzqhStr($jc,$xzqh){
        $buling = [
            '2'=>'0000000000',
            '3'=>'00000000',
            '4'=>'000000',
            '5'=>'000',
            '6'=>''
        ];

        $str= self::getXzqhTrueStr($jc-1 .'*'. $xzqh);
        return $str.$buling[$jc-1];
    }

    static public function getChineseXzqh($xzqh){
        $str= DB::table('t_xzqh')->where('bm',$xzqh)->value('mc');
        $jc=DB::table('t_xzqh')
            ->where('bm','=',$xzqh)
            ->value('jc');
        while ($jc>4){
            $xzqh=self::getLastXzqhStr($jc,$xzqh);
            $jc--;
            $str=DB::table('t_xzqh')
                ->where('bm','=',$xzqh)
                ->value('mc').' '.$str;
        }
        return $str;
    }

    static public function getBmByMc($xzqh){
        return DB::table('t_xzqh')->where('mc',$xzqh)->value('bm');
    }
}