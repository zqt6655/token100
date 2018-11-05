<?php

namespace App;

use Illuminate\Support\Facades\DB;

class DiscussionComment extends BaseModel
{
    //
    public $table='discussion_comment';
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
    public static function get($discussion_id){
        return  DB::table("discussion_comment as dm")
            ->leftJoin('adminuser as ad','dm.user_id','=','ad.id')
            ->where('dm.discussion_id','=',$discussion_id)
            ->where('dm.is_delete','=',0)
            ->select('dm.id','dm.comment','dm.add_time','ad.name','ad.phone','ad.avatar_url')
            ->orderby('dm.id','desc')
            ->get()
            ->toArray();
    }
    public function delete_by_id($id){
        $this::where('id','=',$id)->update(['is_delete'=>1]);
    }
    protected function check_field($data){
        $field = [ 'discussion_id', 'comment'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
