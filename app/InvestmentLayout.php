<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvestmentLayout extends Model
{
    //

    public $table='article';
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
            ->orderBy('id','desc')
            ->paginate($this->perPage);
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
        $field = ['title', 'img', 'link',
            'summary', 'industry_id','user_id',
        ];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
