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

class AdapteeField{

    function add($data){
        $info = Db::name('sfdp_field')->insertGetId($data);
        if($info){
            return  $info;
        }else{
            return  false;
        }
    }
	function add_all($data){
		$info = Db::name('sfdp_field')->insertAll($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function select($map){
		return Db::name('sfdp_field')->where($map)->select()->toArray();
	}
	function saveWhere($where,$data){
		$info = Db::name('sfdp_field')->where($where)->update($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function find($id){	
		$info = Db::name('sfdp_field')->find($id);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function value($id){	
		$info = Db::name('sfdp_field')->where('id',$id)->value('name');
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function findWhere($map){
		$info = Db::name('sfdp_field')->where($map)->find();
		if($info){
			return  $info;
		}else{
			return  false;
		}
		
	}
	
	
}