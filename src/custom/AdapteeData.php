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

class AdapteeData{

	function add($table,$data){
		$info = Db::name($table)->insertGetId($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function find($table,$id){
		$info = Db::name($table)->find($id);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function select($table,$map=[],$field='',$page=1,$limit=10){
		$offset = ($page-1)*$limit;  
		$list = Db::name($table)->where($map)->limit($offset,$limit)->field('id,'.$field.',status')->order('id desc')->select()->toarray();
		$count = Db::name($table)->where($map)->count();
		return ['data'=>$list,'count'=>$count];
	}
	
	
}