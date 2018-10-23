<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Industry extends Model
{
    public $timestamps = false;
    public function get_industry_list(){
//        return self::all()->orderBy('order')->toArray();
        return DB::table('industries')
            ->select('id','name')
            ->where('state','=',0)
            ->orderBy('order')->get();
    }
    //
}
