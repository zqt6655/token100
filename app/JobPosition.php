<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    //
    public $table='job_position';
    public function get(){
        return $this::all()
            ->makeHidden(['is_delete','order'])
            ->toArray();
    }
}
