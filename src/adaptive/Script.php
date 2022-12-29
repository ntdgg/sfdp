<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 脚本类
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
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
    public static function getVer($id){
        return (new Script())->mode->getVer($id);
    }
	/**
	 * 脚本查询
	 */
	public static function script($sid){
		return (new Script())->mode->findWhere([['sid','=',$sid]]);
	}
	/**
	 * 脚本保存
	 */
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
			(new Script())->mode->update(['id'=>$info['id'],'s_fun'=>$data['function'],'add_time'=>time()]);
			$bill=$info['s_bill'];
		}
		$map[] = ['sid','=',$data['sid']];
		$map[] = ['status','=',1];
		$data = Design::updateVerWhere($map,['s_fun_id'=>$id,'s_fun_ver'=>$bill]);
		return $bill;
	}
}