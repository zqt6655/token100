<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Industry extends Model
{
    public function get_list(){
        return $this::where('is_delete','=',0)
            ->orderBy('order')
            ->get()
            ->makeHidden(['is_delete','order','time'])
            ->toArray();
//        return self::all()->orderBy('order')->toArray();
//        return DB::table('industries')
//            ->select('id','name')
//            ->where('state','=',0)
//            ->orderBy('order')->get();
    }
    //
}
