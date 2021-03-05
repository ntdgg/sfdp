<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 数据类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\adaptive;

use sfdp\fun\SfdpUnit;
use sfdp\lib\unit;

class Common{
	
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('db_mode')==1){
			$className = '\\sfdp\\custom\\AdapteeCommon';
		}else{
			$className = unit::gconfig('db_namespace').'AdapteeCommon';
		}
		$this->mode = new $className();
    }
	/**
	 * 查询语句
	 * @param query $sql sql语句
	 */
	static function query($sql)
    {
		return (new Common())->mode->query($sql);
    }
	/**
	 * 查询语句
	 * @param query $sql sql语句
	 */
	static function execute($sql)
    {
		return (new Common())->mode->execute($sql);
    }
	
	
}