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
	
	function getDesignVer($status=1){
		return Db::name('sfdp_design_ver')->where('status',$status)->select();
	}

	
	
}