<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cache extends Model
{
    //
    public $table='cache';
    public function add($data){
        $id = DB::table($this->table)->insertGetId($data);
        if($id>0){
            return true;
        }else{
            return false;
        }
    }
    public function get($key){
        return $this::where('key','=',$key)->one()->toArray();
    }
}
