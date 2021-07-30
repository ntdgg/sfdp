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
	function edit($table,$data,$id){
		$info = Db::name($table)->where('id',$id)->update($data);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function del($table,$id){
		$info = Db::name($table)->delete($id);
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
	function delSub($table,$id){
		if(Db::name($table)->where('d_id',$id)->count()<=0){
	        return  true;
	    }
		$info = Db::name($table)->where('d_id',$id)->delete();
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	function selectAll($table,$map=[]){
		return Db::name($table)->where($map)->withoutField('id,d_id,uid,status,create_time,update_time')->select()->toarray();
	}
	function select($table,$map=[],$field='',$page=1,$limit=10){
		$offset = ($page-1)*$limit;
		if($field!=''){
			$field =','.$field.',';
		}else{
			$field =',';
		}
		$list = Db::name($table)->where($map)->limit($offset,$limit)->field('id'.$field.'status,"url",uid,create_time')->order('id desc')->select()->toarray();
		$count = Db::name($table)->where($map)->count();
		return ['data'=>$list,'count'=>$count];
	}
	
	
}