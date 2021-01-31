<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 脚本类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\adaptive;

use sfdp\lib\unit;

class Script{
	
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('db_mode')==1){
			$className = '\\sfdp\\custom\\AdapteeScript';
		}else{
			$className = unit::gconfig('db_namespace').'AdapteeScript';
		}
		$this->mode = new $className();
    }
	public static function script($sid){
		return (new Script())->mode->findWhere([['sid','=',$sid]]);
	}
	public static function scriptSave($data){
		$info = self::script($data['sid']);
		if(!$info){
			$ver = [
				's_bill'=>unit::OrderNumber(),
				'add_user'=>'Sys',
				'sid'=>$data['sid'],
				's_fun'=>$data['function'],
				'add_time'=>time()
			];
			$id = (new Script())->mode->insert($ver);
			$bill = $ver['s_bill'];
		}else{
			$id = $info['id'];
			(new Script())->mode->update(['id'=>$info['id'],'s_fun'=>$data['function']]);
			$bill=$info['s_bill'];
		}	
		$map[] = ['sid','=',$info['sid']];
		$map[] = ['status','=',1];
		$data = Design::updateVerWhere($map,['s_fun_id'=>$id,'s_fun_ver'=>$info['s_bill']]);
		return $bill;
	}
}