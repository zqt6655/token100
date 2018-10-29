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
        $data = $this::where('project_id','=',$project_id)
            ->first();
        if(!$data){
            return [];
        }
        $data = $data->toArray();
        $project_lab_model = new ProjectLab();
        $project_lab_info = $project_lab_model->get($project_id);
        $data['project_lab'] = $project_lab_info;
        return $data;
    }

    public function update_by_id($data,$id){
        $data = $this->check_field($data);
        if(!$data){
            $this->returnApiError('请传入需要修改的字段');
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
            'accept_coin', 'limit_zone','sorf_cap', 'hard_cap', 'is_kyc','is_aml', 'ratio'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
