<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FoundProject extends BaseModel
{
    //
    public $table='found_project';
    public $timestamps = false;
    public $perPage = 10;
    public function get_by_found_id($found_id){
        $data = DB::table("$this->table as fp")
            ->leftJoin('found as f','fp.found_id','=','f.id')
            ->where('fp.is_delete','=',0)
            ->where('fp.found_id','=',$found_id)
            ->select('pf.found_id','pf.project_id','pf.op_type','pf.price','pf.num','pf.user_id','f.name','f.account','f.unit')
            ->get()
            ->toArray();
    }
    public function get_by_project_id($project_id){
        $data = DB::table("$this->table as fp")
            ->leftJoin('project as p','fp.project_id','=','p.id')
            ->where('fp.is_delete','=',0)
            ->where('fp.project_id','=',$project_id)
            ->select('pf.found_id','pf.project_id','pf.op_type','pf.price','pf.num','pf.user_id','p.name')
            ->get()
            ->toArray();
    }
}
