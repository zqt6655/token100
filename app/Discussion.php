<?php

namespace App;


use Illuminate\Support\Facades\DB;
use function MongoDB\BSON\toJSON;

class Discussion extends BaseModel
{
    //
    public $table='discussion';
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
        return  DB::table("$this->table as d")
            ->leftJoin('adminuser as ad','user_id','=','ad.id')
            ->where('d.is_delete','=',0)
            ->select('d.id','d.title','d.add_time','ad.name','ad.phone')
            ->orderby('d.id','desc')
            ->paginate($this->perPage);
    }
    public function detail($id){
        $info=  DB::table("$this->table as d")
            ->leftJoin('adminuser as ad','d.user_id','=','ad.id')
            ->where('d.id','=',$id)
            ->select('d.id','d.title','d.content','d.pics','d.add_time','ad.name','ad.phone','ad.avatar_url')
            ->first();
        if($info){
            if(!$info->name){
                $info->name = $info->phone;
            }
            if(!$info->pics){
                $info->pics = [];
            }else{
                $info->pics = explode(',',$info->pics);
            }
            $dis_com =DiscussionComment::get($id);
            $info->comment = $dis_com;
            return($info);
        }
        return [];

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
        $field = [ 'title', 'content','pics'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
