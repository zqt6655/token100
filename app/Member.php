<?php

namespace App;



use Illuminate\Support\Facades\DB;

class Member extends BaseModel
{
    //
    public $table='member';
    public $timestamps = false;
    public $perPage = 10;

    public function add($data){
        $data = $this->check_field($data);
        $data = $this->add_date_to_data($data);
        //说明字段没有空值，插入数据库即可。
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重试一次。');
        }
        return [];
    }
    public function get(){
        return $this::where('is_delete','=',0)
            ->orderBy('order')
            ->select('id','member_name','member_position','member_introduce','avatar_url','order')
            ->get()
            ->toArray();
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
        $field = [ 'member_name', 'member_position','member_introduce','avatar_url','order'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
