<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 适配器设计器数据类
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
  *+------------------
  */
namespace sfdp\custom;

use think\facade\Db;

use sfdp\fun\BuildFun;
use sfdp\fun\SfdpUnit;
use sfdp\fun\BuildTable;

use sfdp\lib\unit;


class AdapteeScript{

    function getVer($id){
        $info = Db::name('sfdp_script')->where('id',$id)->value('add_time');
        if($info){
            return  $info;
        }else{
            return  false;
        }
    }

	function findWhere($map){
		$info = Db::name('sfdp_script')->where($map)->find();
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function update($data){
		$info = Db::name('sfdp_script')->update($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function insert($data){
		$info = Db::name('sfdp_script')->insertGetId($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
}