<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    //
    public $table='job_title';
    public function get(){
        return $this::all()
            ->makeHidden(['is_delete','order'])
            ->toArray();
    }
}
