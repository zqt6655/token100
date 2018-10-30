<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Project extends BaseModel
{
    //
    public $table='project';
    public $timestamps = false;
    public $perPage = 10;

    public function add($data){
        $data = $this->check_field($data);
        $upload_time = date('Y-m-d H:i:s');
        $data['upload_time'] = $upload_time;
        //说明字段没有空值，插入数据库即可。
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重试一次。');
        }
        //插入项目详情，预先获取到详情id
        $detail_data['project_id'] = $id;
        $detail_data['upload_time'] = $upload_time;
        $detail_id =  ProjectDetail::add($detail_data);
        //返回项目id和详情id
        $new_data['project_id'] = $id;
        $new_data['project_detail_id'] = $detail_id;
        return $new_data;
    }
    public function get(){
       return  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->where('p.is_delete','=',0)
            ->select('p.*','i.name as industry_id_text')
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
        $field = [ 'name', 'company_name', 'token_symbol', 'foundle', 'website', 'logo', 'country', 'grade', 'analysis',
            'opinion', 'industry_id', 'requirements', 'refer_name', 'refer_introduce', 'domain_from'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
