<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 适配器设计器数据类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\custom;

use think\Db;

use sfdp\fun\BuildFun;
use sfdp\fun\SfdpUnit;
use sfdp\fun\BuildTable;

use sfdp\lib\unit;


class AdapteeDesign{

	function find($sid){
		$info = Db::name('sfdp_design')->find($sid);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function findVer($sid){
		$info = Db::name('sfdp_design_ver')->find($sid);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function update($data){
		$info = Db::name('sfdp_design')->update($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function insert($data){
		$info = Db::name('sfdp_design')->insertGetId($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function getDesignVer($status=1){
		return Db::name('sfdp_design_ver')->where('status',$status)->select();
	}

	
	
}