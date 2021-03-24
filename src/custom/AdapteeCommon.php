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
use think\Exception;

class AdapteeCommon{

	function query($sql){
		try{
			$data = Db::query($sql);
			return ['code'=>0,'msg'=>$data];
		}catch(\Exception $e){
			return ['code'=>-1,'msg'=>'SQL_Err:'.$sql];
		}
	}
	function execute($sql){
		try{
			$data = Db::execute($sql);
			return ['code'=>0,'msg'=>$data];
		}catch(\Exception $e){
			return ['code'=>-1,'msg'=>'SQL_Err:'.$sql];
		}
		
	}
	
	
	
}