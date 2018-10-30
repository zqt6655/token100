<?php

namespace App;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Adminuser extends BaseModel
{
    //
    public $table='adminuser';
    public $timestamps = false;

    public function add($data){
        $data = $this->check_field($data);
        //过滤不属于数据库中的字段，然后将密码加密
        $data['password'] = Hash::make($data['password']);
        $data = $this->add_date_to_data($data);
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重新发送验证码，再试一次。');
        }
        return $id;
    }
    public function checkPhoneIsAvailable($phone){
        $exist = $this::where('phone','=',$phone)
            ->select('id')
            ->first();
        if($exist){
            $this->returnApiError('手机号已注册，请直接登录');
        }
    }
    public function checkEmailIsAvailable($email){
        $exist = $this::where('email','=',$email)
            ->select('id')
            ->first();
        if($exist){
            $this->returnApiError('该邮箱已经被绑定');
        }
    }
    public function get(){
        return $this::where('is_delete','=',0)
            ->orderBy('publish_time','desc')
            ->select('id','title', 'author', 'summary', 'publish_time','img','status')
            ->paginate($this->perPage);
    }

    public function detail($id){
        return $this::where('id','=',$id)
            ->get()
            ->toArray();
    }

    public function update_by_id($data,$id){
        $data = $this->check_field($data);
        //说明字段没有空值，插入数据库即可。
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);
    }
    protected function check_field($data){
        $field = ['email', 'phone', 'name',
            'password', 'avatar_url'
        ];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
