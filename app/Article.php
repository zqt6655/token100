<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Article extends BaseModel
{

    public $table='article';
    public $timestamps = false;
    public $perPage = 10;

    public function add($data){
        $data = $this->check_field($data);
        //说明字段没有空值，插入数据库即可。
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
           $this->returnApiError('系统繁忙,请重试一次');
        }
    }
    public function get(){
        dd($this->user_id);
        return $this::where('is_delete','=',0)
            ->orderBy('publish_time','desc')
            ->select('id','title', 'author', 'summary', 'publish_time','img','status')
            ->paginate($this->perPage);
    }
    public function get_publish(){
        return $this::where('is_delete','=',0)
            ->where('status','=',1)
            ->orderBy('publish_time','desc')
            ->select('id','title', 'author', 'summary', 'publish_time','img')
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
        $field = ['title', 'author', 'summary',
            'publish_time', 'content','user_id','img'
        ];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        //获取当前时间的时分秒
        $hour_minute = date('H:i:s');
        $data['publish_time'] = $data['publish_time'].' '.$hour_minute;
        return $data;
    }
}
