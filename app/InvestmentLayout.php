<?php

namespace App;

use Illuminate\Support\Facades\DB;

class InvestmentLayout extends BaseModel
{
    //

    public $table='investment_layout';
    public $timestamps = false;
    public $perPage = 5;

    public function add($data){
        $data = $this->check_field($data);
        //说明字段没有空值，插入数据库即可。
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->user_id;
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重试');
        }
    }
    public function get(){
        return DB::table("$this->table as in")
            ->leftJoin('category as c','in.category_id','=','c.id')
            ->where('in.is_delete','=',0)
            ->select('in.*','c.name as category_id_text')
            ->orderBy('in.id','desc')
            ->paginate($this->perPage);
    }
    public function get_by_category(){
        $data= DB::table("$this->table as in")
            ->leftJoin('category as c','in.category_id','=','c.id')
            ->where('in.is_delete','=',0)
            ->select('in.*','c.name as category_id_text')
            ->orderBy('in.id','desc')
            ->get() ->toArray();
//            ->toSql();
        if($data){
            $new_data = [];
           foreach ($data as $one){
               $new_data[$one->category_id_text][] = $one;
           }
           return $new_data;
        }
        return $data;
    }
    public function get_by_alp(){

        $data =  $this::where('is_delete','=',0)
            ->orderBy('id','desc')
            ->select('id','title','img','link','summary')
            ->get()
            ->toArray();
        if($data){
            $new_data = [];
            foreach ($data as $one){
                $first_alp = $this->getFirstCharter($one['title']);
                $new_data[$first_alp][] = $one;
            }
            ksort($new_data);
            return $new_data;
        }
        return $data;
    }
    public function delete_by_id($id){
        $this::where('id','=',$id)->update(['is_delete'=>1]);
    }
    public function update_by_id($data,$id){
        $data = $this->check_field($data);
        //说明字段没有空值，插入数据库即可。
        $result =  DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);

    }
    protected function check_field($data){
        $field = ['title', 'img', 'link',
            'summary', 'category_id','user_id','token_symbol'
        ];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
