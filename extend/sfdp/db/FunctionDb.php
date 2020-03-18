<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V3.0
 *+------------------
 * Copyright (c) 2018~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace sfdp;

use think\Db;
use think\facade\Session;

class FunctionDb{
	
	public static function functionSave($data){
		if(!isset($data['id'])){
			$ver = [
				'bill'=>OrderNumber(),
				'title'=>$data['title'],
				'fun_name'=>$data['name'],
				'add_user'=>'Sys',
				'function'=>$data['fun'],
				'add_time'=>time()
			];
			$id = Db::name('sfdp_function')->insertGetId($ver);
			return json(['code'=>0,'msg'=>'操作成功！']);
		}else{
			$ver = [
				'id'=>$data['id'],
				'fun_name'=>$data['name'],
				'function'=>$data['fun']
			];	
			Db::name('sfdp_function')->update($ver);
			return json(['code'=>0,'msg'=>'操作成功！']);
		}
	}
}