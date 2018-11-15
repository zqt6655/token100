<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Lab extends BaseModel
{
    //
    public $table='lab';
    public $timestamps = false;
    public $perPage = 10;

    public function add($data){
        $data = $this->check_field($data);
        //说明字段没有空值，插入数据库即可。
        $data['user_id'] = $this->user_id;
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重试一次。');
        }
        $new_data['id'] = $id;
        return $new_data;
    }
    public function get(){
        $data = $this::where('is_delete','=',0)
            ->orderBy('id')
            ->select('id','parent_id','lab_name')
            ->get()->toArray();
        if(!$data){
            return [];
        }
        $new_data = [];
        $parent_info = [];
        foreach ($data as $key=>$one){
            if($one['parent_id']==0){
                $parent_info[$one['id']] = $one['lab_name'];
            }
        }
        foreach ($parent_info as $k=>$val){
            $new_data[$val]['id'] = $k;
            $new_data[$val]['sub']=[] ;
            foreach ($data as $key=>$one){
                if($one['parent_id']==$k){
                    $new_data[$val]['sub'][]= $one;
                }
            }
        }
        return $new_data;
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
        $field = [ 'parent_id', 'lab_name'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
