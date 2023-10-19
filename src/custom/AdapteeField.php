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

    function findFidVal($fid,$field='sid'){
        $info = Db::name('sfdp_field')->where('fid',$fid)->order('id desc')->value($field);
        if($info){
            return  $info;
        }else{
            return  false;
        }
    }
    function viewFieldIndex($id){
        return Db::name('sfdp_field_index')->find($id);
    }
    function selectFieldIndex($sid){
        return Db::name('sfdp_field_index')->where('sid',$sid)->select()->toArray();
    }
    function delFieldIndex($id){
        return Db::name('sfdp_field_index')->delete($id);
    }
    function addFieldIndex($data){
        $info = Db::name('sfdp_field_index')->insertGetId($data);
        if($info){
            return  $info;
        }else{
            return  false;
        }
    }
}