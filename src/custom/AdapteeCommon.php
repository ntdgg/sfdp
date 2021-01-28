<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 适配器基础数据类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\custom;

use think\facade\Db;

class AdapteeCommon{

	function query($sql){
		return Db::query($sql)
	}
	function execute($sql){
		return Db::execute($sql)
	}
	
	
	
}