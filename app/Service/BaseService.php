<?php

namespace App\Service;

use App\Tools\SqlTool;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BaseService
{

    protected $tbName;

    public function create($data)
    {
        DB::table($this->tbName)->insert($data);
    }

    public function createGetId($data){
        return DB::table($this->tbName)->insertGetId($data);
    }

    public function delete($id)
    {
        DB::table($this->tbName)->where('id', $id)->delete();
    }

    public function update($id, $data)
    {
        DB::table($this->tbName)->where('id', $id)->update($data);
    }

    public function show(array $conditions,$column='*')
    {

        $data = DB::table($this->tbName)->select($column)->where($conditions)->get();
        return $data;
    }

    public function showWithPaginate(array $conditions, int $num)
    {
        $data = DB::table($this->tbName)->where($conditions)->paginate($num);
        return $data;
    }

    /**
     * @param $data
     * 保存数据，记录时间
     */
    public function save($data)
    {
        $data['id'] = SqlTool::makeUUID();
        $data['dtxsj'] = Carbon::now();
        $this->create($data);
    }

    public function isColumnExist($column,$value){
        $check=DB::table($this->tbName)->where($column,$value)->first();
        if ($check){
            return true;
        }
        else{
            return false;
        }
    }
}