<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;

use App\Model\CatModel;
class CatController extends CommonController
{
    public function cat(){
        $data = CatModel::get();
        return $this->success($data);
    }
}
