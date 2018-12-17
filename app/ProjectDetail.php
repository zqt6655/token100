<?php

namespace App;
use Illuminate\Support\Facades\DB;
class ProjectDetail extends BaseModel
{
    //
    public $table='project_details';
    public $timestamps = false;
    public $perPage = 10;

    //项目在创建的时候，同时创建项目详情id
    public static function add($data){
        $detail_id = DB::table('project_details')->insertGetId($data);
        return $detail_id;
    }
    public function get($project_id){
        $data =  DB::table("$this->table as pd")
            ->leftJoin('project as p','pd.project_id','=','p.id')
            ->leftJoin('industries as i','p.industry_id','=','i.id')
            ->select('pd.*','p.*','i.name as industry_id_text')
            ->where('pd.project_id','=',$project_id)
            ->first();
        if(!$data){
            return [];
        }
        if($data->is_market==1){
            $data->is_ing = '已结束';
        }else{
            $now = date('Y-m-d H:i:s');
            if($data->end_time<$now){
                $data->is_ing = '已结束';
            }elseif($data->start_time<$now && $data->end_time>$now){
                $data->is_ing = '进行中';
            }else{
                $data->is_ing = '未开始';
            }
        }


        $data->start_time = substr($data->start_time,0,-3);
        $data->end_time = substr($data->end_time,0,-3);

        $white_book = $data->white_book;
        if($white_book){
            $book_list = explode(',',$white_book);
            $book_tem_list=[];
            foreach ($book_list as $one){
                //后缀
                $pic_ext = ['.jpg', '.png',  '.jpeg'];
                $ext = strrchr($one,'.');
                if($ext=='.pdf')
                    $book_tem['type'] = 'pdf';
                elseif(in_array($ext,$pic_ext))
                    $book_tem['type'] = 'img';
                else
                    $book_tem['type'] = 'url';
                if(strstr($one,'__collinstar__')){
                    $book_tem['show_name'] = explode('__collinstar__',$one)[1];
                    $book_tem['download_url'] = $one;
                }else{
                    $book_tem['show_name'] = substr($one,8,16);
                    $book_tem['download_url'] = $one;
                }
                $book_tem_list[] = $book_tem;
            }
            $data->white_book = $book_tem_list;
        }else{
            $data->white_book=[];
        }
        if($data->team_introduce)
            $data->team_introduce = json_decode($data->team_introduce);
        if($data->score)
            $data->score = json_decode($data->score);
        if($data->project_contacts)
            $data->project_contacts = json_decode($data->project_contacts);
        $project_lab_model = new ProjectLab();
        $project_lab_info = $project_lab_model->get($project_id);
        $data->project_lab= $project_lab_info;
        unset($data->from);
        unset($data->show_name);
        unset($data->is_delete);
        return $data;
    }

    public function update_by_id($data,$id){
        $data = $this->check_field($data);
        if(!$data){
            $this->returnApiError('请传入需要修改的字段');
        }
        //如果更新score，则更新修改时间
        if( isset($data['score']))
            $data['score_time'] = date('Y-m-d H:i:s');
        //说明字段没有空值，插入数据库即可。
        return DB::table($this->table)
            ->where('project_id','=',$id)
            ->update($data);
    }
    public function update_front_by_id($data,$id){
        $data = $this->check_field($data);
        if(!$data){
            $this->returnApiError('请传入需要修改的字段');
        }
        if(isset($data['final'])  && $data['final']==1){
            //如果是最后一页更新，则将前台上传的项目is_delete变为0
            DB::table('project')
                ->where('id','=',$data['project_id'])
                ->update(['is_delete'=>0]);
            unset($data['final']);
            unset($data['project_id']);
        }

        //说明字段没有空值，插入数据库即可。
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);
    }
    protected function check_field($data){
        $field = ['project_contacts','project_introduce', 'problem',
            'framework', 'strength','tokenmodel', 'project_strategy','project_community', 'investplan',
            'project_otherinfo', 'team_introduce', 'investprogress', 'start_time','end_time', 'coin_total','circulate_num', 'platform',
            'accept_coin', 'limit_zone','sorf_cap', 'hard_cap', 'is_kyc','is_aml', 'ratio','final','project_id','score'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
