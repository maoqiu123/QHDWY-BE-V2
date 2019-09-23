<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/6/17
 * Time: 下午5:32
 */

namespace App\Service;


use App\Tools\SqlTool;
use Illuminate\Support\Facades\DB;

class FileService extends BaseService
{
    protected $tbName = 't_fjb';

    public function saveFiles($path, $name)
    {
        $data = [
            'id' => SqlTool::makeUUID(),
            'fjlj' => $path,
            'fjmc' => $name
        ];
        $this->create($data);
        return $data['id'];
    }

    public function getDownloadFiles($bid)
    {
        $files = $this->show([
            ['bid', '=', $bid]
        ], [
                'id', 'fjlj', 'fjmc'
            ]
        );
        if ($files->first() == null)
            return false;
        return $files;
    }

    public function getDownloadFile($rowId)
    {
        $file = DB::table('t_fjb')->where('id', $rowId)->first();
        return $file;
    }

    public function deleteDownloadFile($rowId)
    {
        DB::table('t_fjb')->where('id', $rowId)->delete();
    }

    public function updateTableRecord($id, array $data, $tbName)
    {
        $isExist = DB::table($tbName)->where('id', $id)->first();
        if($isExist == null)
            return false;
        //初始化json
        if($isExist->ffj == null) {
            DB::table($tbName)->where('id', $id)->update(['ffj' => "{}"]);
        }

        DB::table($tbName)->where('id', $id)->update(['ffj->'.$data['id'] => json_encode($data)]);
        return true;
    }

    public function deleteTableRecord($fileid,$rowid,$tbName)
    {
        $files =json_decode(DB::table($tbName)->where('id', $rowid)->value('ffj'));
        unset($files->$fileid);
        $res = DB::table($tbName)->where('id', $rowid)->update(['ffj'=> json_encode($files)]);
        return $res;
    }
}