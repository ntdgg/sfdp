<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V7.0
 *+------------------
 * Sfdp 脚本类
 *+------------------
 * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
 *+------------------
 */
namespace sfdp\adaptive;

use sfdp\lib\unit;

class Userconfig{

    protected $mode ;
    public function  __construct(){
        if(unit::gconfig('db_mode')==1){
            $className = '\\sfdp\\custom\\AdapteeUserconfig';
        }else{
            $className = unit::gconfig('db_namespace').'AdapteeUserconfig';
        }
        $this->mode = new $className();
    }
    /**
     * 获取用户配置信息
     */
    public static function getInfo($id){
        return (new Userconfig())->mode->getInfo($id);
    }
    /**
     * 添加或者更新用户自定义配置
     */
    public static function add($data){
        return (new Userconfig())->mode->add($data);
    }

}