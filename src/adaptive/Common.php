<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 数据类
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
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