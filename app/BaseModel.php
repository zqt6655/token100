<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2018/10/25
 * Time: 20:53
 */

namespace App;


use App\Http\Controllers\ApiException;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function returnApiError($message,$code=-1){
        throw new ApiException($message,$code);
    }

}