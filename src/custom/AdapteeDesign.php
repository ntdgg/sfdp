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

use think\facade\Db;

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
	function select($map,$order){
		return Db::name('sfdp_design')->where($map)->order($order)->select();
	}
	function findVer($sid){
		$info = Db::name('sfdp_design_ver')->alias('a')->leftjoin('sfdp_design d','d.id=a.sid')->field('a.*,d.s_type')->find($sid);
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
	function updateVer($data=[]){
		$info = Db::name('sfdp_design_ver')->update($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function updateVerWhere($map=[],$data=[]){
		$info = Db::name('sfdp_design_ver')->where($map)->update($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function findVerWhere($map){
		$info = Db::name('sfdp_design_ver')->where($map)->find();
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function insertVer($data){
		$info = Db::name('sfdp_design_ver')->insertGetId($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function getDesignVer($status=1){
		return Db::name('sfdp_design_ver')->where('status',$status)->select();
	}
	function insertBtable($data){
		$info = Db::name('sfdp_btable')->insertGetId($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
}