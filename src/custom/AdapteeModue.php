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

class AdapteeModue{

	function add($data){
		$info = Db::name('sfdp_modue')->insertGetId($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function saveWhere($where,$data){
		$info = Db::name('sfdp_modue')->where($where)->update($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function find($id){	
		$info = Db::name('sfdp_modue')->where('sid',$id)->find();
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function findWhere($map){
		$info = Db::name('sfdp_modue')->where($map)->find();
		if($info){
			return  $info;
		}else{
			return  false;
		}
		
	}
	
	
	
}