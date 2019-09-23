<?php
/**
 * Created by PhpStorm.
 * User: trons
 * Date: 2018/6/16
 * Time: 下午5:47
 */

namespace App\Exceptions;

use Exception;

class BaseException extends Exception
{

    /**
     * BaseException constructor.
     */
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }
}