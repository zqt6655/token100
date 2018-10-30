<?php

namespace App;
use Illuminate\Support\Facades\DB;
class ProjectLab extends BaseModel
{
    //
    public $table='project_lab';
    public $timestamps = false;
    public $perPage = 10;

    //项目在创建的时候，同时创建项目详情id
    public  function add($lab_id,$project_id){
        //先将数据库中的对应项目的标签删除，然后添加新的
        DB::table("$this->table")->where('project_id','=',$project_id)->delete();
        //将字符串转换成数组，然后组装成二维数组，一次性插入到数据库中
        $lab_ids = explode(',',$lab_id);
        $data = [];
        foreach ($lab_ids as $id){
            $tem['project_id'] = $project_id;
            $tem['lab_id'] = $id;
            $data[] = $tem;
        }
        DB::table("$this->table")->insert($data);
    }
    public function get($project_id){
        $lab_ids = $this::where('project_id','=',$project_id)
            ->pluck('lab_id')
            ->toArray();
        if(!$lab_ids){
            $data['lab_ids'] = [];
            $data['lab_name'] = [];
            return $data;
        }
        //查询所有的标签
        $lab_info = DB::table('lab')->where('is_delete','=',0)->select('id','lab_name')->get()->toArray();
        $lab_name=[];
        foreach ($lab_ids as $id){
            foreach ($lab_info as $one){
                if($one->id==$id){
                    $lab_name[]=$one->lab_name;
                    break;
                }
            }
        }
//        $lab_ids = implode(',',$lab_ids);
//        $lab_name = implode(',',$lab_name);
        $data['lab_ids'] = $lab_ids;
        $data['lab_name'] = $lab_name;
        return $data;
    }
}
