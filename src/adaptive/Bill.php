<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V7.0
 *+------------------
 * Sfdp 数据类
 *+------------------
 * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
 *+------------------
 */
namespace sfdp\adaptive;


use sfdp\fun\SfdpUnit;
use sfdp\lib\unit;

class Bill{

    protected $mode ;
    public function  __construct(){
        if(unit::gconfig('db_mode')==1){
            $className = '\\sfdp\\custom\\AdapteeBill';
        }else{
            $className = unit::gconfig('db_namespace').'AdapteeBill';
        }
        $this->mode = new $className();
    }
    /**
     * 查询
     */
    static function findWhere($map){
        return (new Bill())->mode->findWhere($map);
    }
}