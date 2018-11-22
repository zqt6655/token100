<?php

namespace App;


class Found extends BaseModel
{
    //
    public $table='found';
    public $timestamps = false;
    public $perPage = 10;
    public function get(){
        return $this::where('is_delete','=',0)
            ->orderBy('id')
            ->get()
            ->makeHidden(['is_delete','add_time'])
            ->toArray();
    }
}
