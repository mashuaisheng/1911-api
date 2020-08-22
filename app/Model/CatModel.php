<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CatModel extends Model
{
    //指定表名
    protected $table = "cat";
    //指定主键pk
    protected $primaryKey = "cat_id";
    //关闭时间戳
    public $timestamps = false;
    //黑名单
    protected $guarded = [];
}
