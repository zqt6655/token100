<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table='category';
    public function get_list(){
        return $this::where('is_delete','=',0)
            ->orderBy('order')
            ->get()
            ->makeHidden(['is_delete','order'])
            ->toArray();
    }
}
