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
        $data['from'] = 1;
        $data['user_id'] = $this->user_id;
        $data['show_name'] = '后台';
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

    public function add_front($data){
        $data = $this->check_field($data);
        $upload_time = date('Y-m-d H:i:s');
        $data['upload_time'] = $upload_time;
        $data['from'] = 2;
        $data['is_delete'] = 1;
        $data['show_name'] = '前台';
        //说明字段没有空值，插入数据库即可。
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重试一次。');
        }
        //插入项目详情，预先获取到详情id
        $detail_data['project_id'] = $id;
        $detail_data['project_contacts'] = $data['project_contacts'];
        $detail_data['upload_time'] = $upload_time;
        $detail_id =  ProjectDetail::add($detail_data);
        //返回项目id和详情id
        $new_data['project_id'] = $id;
        $new_data['project_detail_id'] = $detail_id;
        return $new_data;
    }
    public function get(){
       $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('p.is_delete','=',0)
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','p.from','p.show_name','i.name as industry_id_text','ad.name as up_name')
           ->orderby('id','desc')
           ->paginate($this->perPage);
       foreach ($data as $one){
           if($one->from !=1){
               $one->up_name = $one->show_name;
           }
           unset($one->show_name);
           unset($one->from);
           unset($one->user_id);
       }
       return $data;
    }
    public function search($keyword){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('p.is_delete','=',0)
            ->where(function($query) use ($keyword){
                $query->where('p.name', 'like', '%'.$keyword.'%')
                    ->orWhere('p.token_symbol', 'like', '%'.$keyword.'%');
            })
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','p.from','p.show_name','i.name as industry_id_text','ad.name as up_name')
            ->paginate($this->perPage);
        foreach ($data as $one){
            if($one->from !=1){
                $one->up_name = $one->show_name;
            }
            unset($one->show_name);
            unset($one->from);
            unset($one->user_id);
        }
        return $data;
    }
    public function get_ioc(){
        $now = date('Y-m-d H:i:s');
        $data = DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('project_details as pd','p.id','=','pd.project_id')
            ->where('p.is_delete','=',0)
            ->where('pd.end_time','>',$now)
            ->select('p.id','p.name','p.logo','p.token_symbol','p.country','i.name as industry_id_text','pd.start_time','pd.end_time')
            ->orderby('id','desc')
            ->get()
            ->toArray();
        if(!$data){
            return $data;
        }
        foreach ($data as $one){
            if($one->start_time<$now){
                $one->is_ing = 1;
            }else{
                $one->is_ing = 0;
            }
            $one->start_time = substr($one->start_time,0,-3);
            $one->end_time = substr($one->end_time,0,-3);
        }
        return $data;
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
            'opinion', 'industry_id', 'requirements', 'refer_name', 'refer_introduce', 'domain_from','white_book','project_contacts'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
