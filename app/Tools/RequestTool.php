<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/16
 * Time: 下午4:14
 */

namespace App\Tools;


class RequestTool
{
    public static function response($data, int $code, string $messgae)
    {
        $body = [
            'code' => $code,
            'message' => $messgae,
            'data' => $data
        ];
//        if ($data !== null && sizeof($data) > 0) {
//            $body['data'] = $data;
//        }
        return response()->json($body);
    }
}