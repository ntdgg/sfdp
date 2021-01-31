<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 方法类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\adaptive;

use sfdp\lib\unit;

class Functions{
	
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('db_mode')==1){
			$className = '\\sfdp\\custom\\AdapteeFunctions';
		}else{
			$className = unit::gconfig('db_namespace').'AdapteeFunctions';
		}
		$this->mode = new $className();
    }
	static function select($map=[],$order='id desc')
    {
		return (new Functions())->mode->select($map,$order);
    }
	static function findWhere($map)
    {
		return (new Functions())->mode->findWhere($map);
    }
	static function update($data)
    {
		return (new Functions())->mode->update($data);
    }
	public static function save($data){
		if(!isset($data['id'])){
			$map[] = ['fun_name','=',$data['name']];
		}else{
			$map[] = [['fun_name','=',$data['name']],['id','<>',$data['id']]];
		}
		$hasname = self::findWhere($map);
		if($hasname){
			return json(['code'=>1,'msg'=>'禁止函数名称重复！']);
		}
		$retFun = Common::query($data['fun']);
		if($retFun['code']==-1){
			return json($retFun);
		}
		if(!isset($data['id'])){
			$ver = [
				'bill'=>unit::OrderNumber(),
				'title'=>$data['title'],
				'fun_name'=>$data['name'],
				'add_user'=>'Sys',
				'function'=>$data['fun'],
				'add_time'=>time()
			];
			$ret = (new Functions())->mode->insert($ver);
			if($ret){
				return json(['code'=>0,'msg'=>'操作成功！']);
			}else{
				return json(['code'=>-1,'msg'=>'更新出错']);
			}
		}else{
			$ver = [
				'id'=>$data['id'],
				'title'=>$data['title'],
				'fun_name'=>$data['name'],
				'function'=>$data['fun']
			];	
			$ret = (new Functions())->mode->update($ver);
			if($ret){
				return json(['code'=>0,'msg'=>'操作成功！']);
			}else{
				return json(['code'=>-1,'msg'=>'更新出错']);
			}
			
		}
	}
}