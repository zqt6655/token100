<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2018/10/23
 * Time: 14:21
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Relationship extends BaseModel
{
    public $table='relationship';
    public $table_name = 'relationship';
    public $timestamps = false;
    public $perPage=10;
    public function get_all(){
        return DB::table("$this->table_name as r")
//            ->leftJoin('category as c','r.category_id','=','c.id')
//            ->where('r.is_delete','=',0)
//            ->select('r.*','c.name as category_id_text')
            ->select('r.*')
            ->orderBy('id','desc')
            ->paginate($this->perPage);
    }
    public function get_by_category_id($category_id){
        return DB::table("$this->table_name as r")
//            ->leftJoin('category as c','r.category_id','=','c.id')
            ->where('r.is_delete','=',0)
            ->where('r.category_id','=',$category_id)
//            ->select('r.*','c.name as category_id_text')
//            ->select('r.*')
            ->orderBy('id','desc')
            ->paginate($this->perPage);
    }
    public function get_detail($id){
        return  DB::table("$this->table_name as r")
            ->leftJoin('category as c','r.category_id','=','c.id')
            ->leftJoin('industries as i','r.industry_id','=','i.id')
            ->where('r.id','=',$id)
            ->select('r.*','c.name as category_id_text','i.name as industry_id_text')
            ->get()
            ->toArray();
//        $data= $this::where('id','=',$id)
//            ->first()
//            return $this::where('is_delete','=',0)
//                ->orderBy('industry_id', 'desc')
//                ->get()
//            ->makeHidden(['is_delete','created_at','updated_at'])
//            ->toArray();
    }

    public function delete_by_id($id){
         DB::table($this->table_name)
            ->where('id','=',$id)
            ->update(['is_delete'=>1]);
    }
    public function add($data){
        $data = $this->check_field($data);
        $data = $this->add_date_to_data($data);
        //说明字段没有空值，插入数据库即可。
        $id = DB::table($this->table_name)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重试一次');
        }

    }
    protected function check_field($data){
        $field = ['name', 'phone', 'wechat',
            'email', 'avatar_url','company',
            'position', 'title','industry_id',
            'category_id','category_name','note','linkman','link_phone','link_wechat'
        ];
//        $empty = 0;
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
    public function update_by_id($data,$id){
        $data = $this->check_field($data);
//        $data['updated_at'] = time();
        //说明字段没有空值，插入数据库即可。
        DB::table($this->table_name)
            ->where('id','=',$id)
            ->update($data);
    }

    public function search($keyword){
        return DB::table("$this->table_name as r")
//            ->leftJoin('category as c','r.category_id','=','c.id')
            ->where('r.is_delete', '=', 0)
//            $this::where('is_delete', '=', 0)
            ->where(function($query) use ($keyword){
                $query->where('r.name', 'like', '%'.$keyword.'%')
                    ->orWhere('r.phone', 'like', '%'.$keyword.'%')
                    ->orWhere('r.email', 'like', '%'.$keyword.'%')
                    ->orWhere('r.company', 'like', '%'.$keyword.'%')
                    ->orWhere('r.position', 'like', '%'.$keyword.'%')
                    ->orWhere('r.title', 'like', '%'.$keyword.'%');
            })

//            ->select('r.*','c.name as category_id_text')
            ->select('r.*')
            ->orderBy('r.id','desc')
            ->paginate($this->perPage);
//            ->get()->makeHidden(['is_delete','created_at','updated_at'])->toArray();

    }


}