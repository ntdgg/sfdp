<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V7.0
 *+------------------
 * Sfdp 适配器基础数据类
 *+------------------
 * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
 *+------------------
 */
namespace sfdp\custom;

use think\facade\Db;

class AdapteeBill{


    function findWhere($map){
        $info = Db::name('sfdp_billno')->where($map)->find();
        if($info){
            return  $info;
        }else{
            return  false;
        }
    }
}