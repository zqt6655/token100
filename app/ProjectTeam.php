<?php

namespace App;


use Illuminate\Support\Facades\DB;
use function MongoDB\BSON\toJSON;

class ProjectTeam extends BaseModel
{
    //
    public $table='project_team';
    public $timestamps = false;

    public function add($data){
        $data = $this->check_field($data);
        //说明字段没有空值，插入数据库即可。
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重试一次。');
        }
        $return_data['team_id'] = $id;
        return $return_data;
    }
    public function get_by_project_id($project_id){
        $data =    $this::where('project_id','=',$project_id)
            ->where('is_delete','=',0)
            ->get()
            ->makeHidden('is_delete')
            ->toArray();
        if($data){
            $new_data = [];
            foreach ($data as $one){
                $one['introduce'] = json_decode($one['introduce'],true);
                $new_data[] = $one;
            }
            return $new_data;
        }
        return [];



    }
    public function update_by_team_id($team_id,$introduce){
        DB::table($this->table)->where('team_id','=',$team_id)
            ->update(['introduce'=>$introduce]);
        return [];

    }
    public function delete_by_team_id($team_id){
        DB::table($this->table)->where('team_id','=',$team_id)
            ->update(['is_delete'=>1]);
        return [];

    }

    protected function check_field($data){
        $field = ['project_id','team_id','introduce'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
