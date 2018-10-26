<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Lab extends Model
{
    //
    public $table='lab';
    public $timestamps = false;
    public $perPage = 10;

    public function add($data){
        $data = $this->check_field($data);
        //说明字段没有空值，插入数据库即可。
        $id = DB::table($this->table)->insertGetId($data);
        if($id>0){
            return true;
        }else{
            return false;
        }
    }
    public function get(){
        return $this::where('is_delete','=',0)
            ->orderBy('publish_time','desc')
            ->paginate($this->perPage);
    }
    public function detail($id){
        return $this::where('id','=',$id)
            ->get()
            ->toArray();
    }
    public function publish_or_cancel($id,$status){
        $this::where('id','=',$id)->update(['status'=>$status]);
    }
    public function delete_by_id($id){
        $this::where('id','=',$id)->update(['is_delete'=>1]);
    }
    public function update_by_id($data,$id){
        $data = $this->check_field($data);
        //说明字段没有空值，插入数据库即可。
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);
    }
    protected function check_field($data){
        $field = ['id', 'parent_id', 'lab_name',
           'user_id'
        ];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
