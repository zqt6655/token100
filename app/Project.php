<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Project extends BaseModel
{
    //
    public $table='project';
    public $timestamps = false;
    public $perPage = 10;
    public function getOpinionAttribute($value){
        //0待上会，1持续观察，2执行孵化，3拒绝
        $Opinion = [0=>'待上会',1=>'持续观察',2=>'执行孵化',3=>'拒绝'];
//        return $arr[$value];

    }
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
    public function get_front(){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->where('p.is_delete','=',0)
            ->where('p.from','=',2)
            ->where('p.is_invest','=',0)
            ->where('p.grade','=','')
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','p.from','p.show_name as up_name','i.name as industry_id_text','p.is_market','p.is_invest')
            ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
            ->paginate($this->perPage);
        return $data;
    }
    public function get_system(){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->where('p.is_delete','=',0)
            ->where('p.from','=',0)
            ->where('p.is_invest','=',0)
            ->where('p.grade','=','')
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','p.from','p.show_name as up_name','i.name as industry_id_text','p.is_market','p.is_invest')
            ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
            ->paginate($this->perPage);
        return $data;
    }

    public function get_back(){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('p.is_delete','=',0)
            ->where('p.from','=',1)
            ->where('p.is_invest','=',0)
            ->where('p.grade','=','')
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','i.name as industry_id_text','ad.name as up_name','p.is_market','p.is_invest')
            ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
            ->paginate($this->perPage);
        return $data;
    }
    public function get_grade(){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('p.is_delete','=',0)
            ->where('p.grade','<>','')
            ->where('p.is_invest','=',0)
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.opinion','p.user_id','i.name as industry_id_text','p.is_market','p.is_invest','p.show_name','ad.name as up_name','p.from')
            ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
            ->paginate($this->perPage);
        $opinion = [0=>'待上会',1=>'持续观察',2=>'投行孵化',3=>'拒绝'];
        foreach ($data as $one){
            $one->opinion = $opinion[$one->opinion];
            if($one->from !=1){
                $one->up_name = $one->show_name;
            }
            unset($one->show_name);
        }
        $continue_num = 0;
        $hatch_num = 0;
        $reject_num = 0;
        $data2 =  DB::table("$this->table as p")
            ->where('p.is_delete','=',0)
            ->where('p.grade','<>','')
            ->where('p.is_invest','=',0)
            ->where('p.opinion','<>',0)
            ->pluck('p.opinion');
//            ->select('p.opinion')
//            ->get()
//            ->toArray();
        foreach ($data2 as $one){
            if($one ==1)
                $continue_num++;
            else if($one==2)
                $hatch_num++;
            else if($one==3)
                $reject_num++;
        }
        $total = $data->total();
        $wait_num = $total - $continue_num - $hatch_num - $reject_num;
        $returnData['total'] = $total;
        $returnData['wait_num'] = $wait_num;
        $returnData['continue_num'] = $continue_num;
        $returnData['hatch_num'] = $hatch_num;
        $returnData['reject_num'] = $reject_num;
        $returnData['data'] = $data;
//        return $returnData;
        return $data;
    }
    public function search_grade($keyword){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('p.is_delete','=',0)
            ->where('p.grade','<>','')
            ->where('p.is_invest','=',0)
            ->where(function($query) use ($keyword){
                $query->where('p.name', 'like', '%'.$keyword.'%')
                    ->orWhere('p.token_symbol', 'like', '%'.$keyword.'%');
            })
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.opinion','p.user_id','i.name as industry_id_text','p.is_market','p.is_invest','p.show_name','ad.name as up_name','p.from')
            ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
            ->paginate($this->perPage);
        foreach ($data as $one){
            if($one->from !=1){
                $one->up_name = $one->show_name;
            }
            unset($one->show_name);
        }
        return $data;

    }
    public function get_wait(){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->where('p.is_delete','=',0)
            ->where('p.opinion','=',0)
            ->where('p.grade','<>','')
            ->where('p.is_invest','=',0)
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','i.name as industry_id_text','p.is_market','p.is_invest')
            ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
            ->paginate($this->perPage);
        $opinion = [0=>'待上会',1=>'持续观察',2=>'投行孵化',3=>'拒绝'];
        foreach ($data as $one){
            $one->opinion = $opinion[$one->opinion];
        }
        $returnData['data'] = $data;
        return $returnData;
//        return $data;
    }

    public function get_continue(){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->where('p.is_delete','=',0)
            ->where('p.opinion','=',1)
            ->where('p.grade','<>','')
            ->where('p.is_invest','=',0)
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','i.name as industry_id_text','p.is_market','p.is_invest')
            ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
            ->paginate($this->perPage);
        $opinion = [0=>'待上会',1=>'持续观察',2=>'投行孵化',3=>'拒绝'];
        foreach ($data as $one){
            $one->opinion = $opinion[$one->opinion];
        }
        $returnData['data'] = $data;
        return $returnData;
//        return $data;
    }
    public function get_hatch(){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->where('p.is_delete','=',0)
            ->where('p.opinion','=',2)
            ->where('p.grade','<>','')
            ->where('p.is_invest','=',0)
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','i.name as industry_id_text','p.is_market','p.is_invest')
            ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
            ->paginate($this->perPage);
        $opinion = [0=>'待上会',1=>'持续观察',2=>'投行孵化',3=>'拒绝'];
        foreach ($data as $one){
            $one->opinion = $opinion[$one->opinion];
        }
        $returnData['data'] = $data;
        return $returnData;
//        return $data;
    }
    public function get_reject(){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->where('p.is_delete','=',0)
            ->where('p.opinion','=',3)
            ->where('p.grade','<>','')
            ->where('p.is_invest','=',0)
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','i.name as industry_id_text','p.is_market','p.is_invest')
            ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
            ->paginate($this->perPage);
        $opinion = [0=>'待上会',1=>'持续观察',2=>'投行孵化',3=>'拒绝'];
        foreach ($data as $one){
            $one->opinion = $opinion[$one->opinion];
        }
        $returnData['data'] = $data;
        return $returnData;
//        return $data;
    }

    public function get(){
       $data=  DB::table("$this->table as p")
//           ->leftJoin('project_details as pd','pd.project_id','=','p.id')
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('p.is_delete','=',0)
            ->where('p.is_invest','=',0)
           ->where('p.grade','=','')
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','p.from','p.show_name','i.name as industry_id_text','ad.name as up_name','p.is_market','p.is_invest')
           ->orderby('p.id','desc')
//           ->simplePaginate($this->perPage);
           ->paginate($this->perPage);
        $opinion = [0=>'待上会',1=>'持续观察',2=>'执行孵化',3=>'拒绝'];
       foreach ($data as $one){
           if($one->from !=1){
               $one->up_name = $one->show_name;
           }
           $one->opinion = $opinion[$one->opinion];
           unset($one->from);
       }
//       $front_num = 0;
//       $back_num = 0;
//       查询前台和后台项目总和
//        $data2 =  DB::table("$this->table as p")
//            ->where('p.is_delete','=',0)
//            ->where('p.is_invest','=',0)
//            ->where('p.grade','=','')
//            ->where('p.from','<>',0)
//            ->select('from')
//            ->get()
//            ->toArray();
//        foreach ($data2 as $one){
//            if($one->from ==1)
//                $back_num++;
//            else
//                $front_num++;
//        }
//        $total = $data->total();
//        $returnData['total'] = $total;
//        $returnData['front_num'] = $front_num;
//        $returnData['back_num'] = $back_num;
//        $returnData['system_num'] = $total-$front_num-$back_num;
//        $returnData['data'] = $data;
        return $data;
//       return $returnData;
    }
    //查询项目池
    public function search($keyword){
        $data=  DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('p.is_delete','=',0)
            ->where('p.is_invest','=',0)
            ->where('p.grade','=','')
            ->where(function($query) use ($keyword){
                $query->where('p.name', 'like', '%'.$keyword.'%')
                    ->orWhere('p.token_symbol', 'like', '%'.$keyword.'%');
            })
            ->select('p.id','p.name','p.logo','p.token_symbol','p.upload_time','p.requirements','p.grade','p.analysis',
                'p.opinion','p.user_id','p.from','p.show_name','i.name as industry_id_text','ad.name as up_name','p.is_invest')
            ->paginate($this->perPage);
        foreach ($data as $one){
            if($one->from !=1){
                $one->up_name = $one->show_name;
            }
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
            ->where('p.is_invest','=',0)
            ->where('p.grade','=','')
            ->where('pd.end_time','>',$now)
            ->select('p.id','p.name','p.logo','p.token_symbol','p.country','i.name as industry_id_text','pd.start_time','pd.end_time','p.is_invest')
            ->orderby('p.id','desc')
            ->paginate($this->perPage);
        if(!$data){
            return [];
        }
        foreach ($data as $one){
            if($one->start_time<$now){
                $one->is_ing = '进行中';
            }else{
                $one->is_ing = '未开始';
            }
            $one->start_time = substr($one->start_time,0,-3);
            $one->end_time = substr($one->end_time,0,-3);
        }
        return $data;
    }

    public function search_ico($keyword){
        $now = date('Y-m-d H:i:s');
        $data = DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('project_details as pd','p.id','=','pd.project_id')
            ->where('p.is_delete','=',0)
            ->where('p.is_invest','=',0)
            ->where('p.grade','=','')
            ->where('pd.end_time','>',$now)
            ->where(function($query) use ($keyword){
                $query->where('p.name', 'like', '%'.$keyword.'%')
                    ->orWhere('p.token_symbol', 'like', '%'.$keyword.'%');
            })
            ->select('p.id','p.name','p.logo','p.token_symbol','p.country','i.name as industry_id_text','pd.start_time','pd.end_time','p.is_invest')
            ->orderby('p.id','desc')
            ->paginate($this->perPage);
        if(!$data){
            return [];
        }
        foreach ($data as $one){
            if($one->start_time<$now){
                $one->is_ing = '进行中';
            }else{
                $one->is_ing = '未开始';
            }
            $one->start_time = substr($one->start_time,0,-3);
            $one->end_time = substr($one->end_time,0,-3);
        }
        return $data;
    }
    //获取已经转入投资的项目
    public function get_invest(){
        $data = DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('p.is_delete','=',0)
            ->where('p.is_invest','=',1)
            ->select('p.id','p.name','p.logo','p.token_symbol','p.show_name','ad.name as up_name','p.from','i.name as industry_id_text','p.requirements')
            ->orderby('p.id','desc')
            ->paginate($this->perPage);
        if(!$data){
            return [];
        }
//        return $data;
        //保存分页中项目的id
        $ids = [];
        foreach ($data as $one){
            $ids[]= $one->id;
            if($one->from !=1){
                $one->up_name = $one->show_name;
            }
            unset($one->show_name);
            unset($one->from);
        }
        //去投资记录中，查询对应的回币记录
        $record = DB::table("found_project")
            ->whereIn('project_id',$ids)
            ->where('op_type','=',0)
            ->select('project_id','num','status','pay_coin_time')
            ->get()
            ->toArray();
        foreach ($data as $one){
            $one->pay_coin_time='';
            $one->should_back=0;
            $one->back=0;
            foreach ($record as $key=>$r){
                if($one->id == $r->project_id){
                    $one->pay_coin_time = $r->pay_coin_time;
                    if($r->status==1)
                        $one->should_back +=$r->num;
                    else
                        $one->back +=$r->num;
                    unset($record[$key]);
                }
            }
            if($one->should_back==0)
                $one->status= '待打币';
            elseif ($one->should_back==$one->back)
                $one->status= '已回币';
            else
                $one->status= '待回币';
            unset($one->should_back);
            unset($one->back);
        }

        return $data;
    }
    public function search_wait_back(){

    }
    //搜索已经转入投资的项目
    public function search_invest($keyword){
        $data = DB::table("$this->table as p")
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('p.is_delete','=',0)
            ->where('p.is_invest','=',1)
            ->where(function($query) use ($keyword){
                $query->where('p.name', 'like', '%'.$keyword.'%')
                    ->orWhere('p.token_symbol', 'like', '%'.$keyword.'%');
            })
            ->select('p.id','p.name','p.logo','p.token_symbol','p.show_name','ad.name as up_name','p.from','i.name as industry_id_text','p.requirements')
            ->orderby('p.id','desc')
            ->paginate($this->perPage);
        if(!$data){
            return [];
        }
//        return $data;
        //保存分页中项目的id
        $ids = [];
        foreach ($data as $one){
            $ids[]= $one->id;
            if($one->from !=1){
                $one->up_name = $one->show_name;
            }
            unset($one->show_name);
            unset($one->from);
        }
        //去投资记录中，查询对应的回币记录
        $record = DB::table("found_project")
            ->whereIn('project_id',$ids)
            ->where('op_type','=',0)
            ->select('project_id','num','status','pay_coin_time')
            ->get()
            ->toArray();
        foreach ($data as $one){
            $one->pay_coin_time='';
            $one->should_back=0;
            $one->back=0;
            foreach ($record as $key=>$r){
                if($one->id == $r->project_id){
                    $one->pay_coin_time = $r->pay_coin_time;
                    if($r->status==1)
                        $one->should_back +=$r->num;
                    else
                        $one->back +=$r->num;
                    unset($record[$key]);
                }
            }
            if($one->should_back==0)
                $one->status= '待打币';
            elseif ($one->should_back==$one->back)
                $one->status= '已回币';
            else
                $one->status= '待回币';
            unset($one->should_back);
            unset($one->back);
        }

        return $data;
    }
    //项目撤离投资
    public function invest_off($id){
        return DB::table("$this->table")
            ->where('id','=',$id)
            ->update(['is_invest'=>0,'invest_user_id'=>$this->user_id]);
    }
    //项目转入投资
    public function invest_on($id){
        return DB::table("$this->table")
            ->where('id','=',$id)
            ->update(['is_invest'=>1,'invest_user_id'=>$this->user_id]);
    }

    public function delete_by_id($id){
        return $this::where('id','=',$id)->update(['is_delete'=>1]);
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
