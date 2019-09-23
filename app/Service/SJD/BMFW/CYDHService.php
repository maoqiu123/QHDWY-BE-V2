<?php

namespace App\Service\SJD\BMFW;

use Illuminate\Support\Facades\DB;

class CYDHService
{
    private $tbName = 't_crdh';
    public function getCydh($xmid)
    {
        $cydh = DB::table($this->tbName)
            ->where('sxmid',$xmid)
            ->get();
        return $cydh;
    }
}
