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