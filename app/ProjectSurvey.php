<?php

namespace App;


use Illuminate\Support\Facades\DB;

class ProjectSurvey extends BaseModel
{
    //
    public $table='project_survey';
    public $timestamps = false;
    public $perPage = 10;

    public function add($data){
        $data = $this->check_field($data);
        $data = $this->add_date_to_data($data);
        $data['user_id'] = $this->user_id;
        //说明字段没有空值，插入数据库即可。
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重试一次。');
        }
        $returnData['id'] = $id;
        return $returnData;
    }
    public function get($project_id){
        return $this::where('project_id','=',$project_id)
            ->where('is_delete','=',0)
            ->get()
            ->makeHidden('is_delete')
            ->toArray();
    }
    public function update_by_id($data,$id){
        $data = $this->check_field($data);
        return DB::table($this->table)->where('id','=',$id)
            ->update($data);
    }
    public function delete_by_id($id){
        return DB::table($this->table)->where('id','=',$id)
            ->update(['is_delete'=>1]);
    }
    protected function check_field($data){
        $field = [ 'project_id', 'title','url','survey_man'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
