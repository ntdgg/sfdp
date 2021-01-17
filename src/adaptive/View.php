<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 视图类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\adaptive;

use sfdp\lib\unit;

class View{
	
	public static function ver($sid){
		$json = Design::findVerWhere([['status','=',1],['sid','=',$sid]]);
		$field = json_decode($json['s_field'],true);
		foreach($field['list'] as $k=>$v){
			foreach($v['data'] as $v2){
				if(isset($v2['tpfd_db'])){
					$data_ver_db[] = $v2;
				}
			}
		}
		return ['db'=>$data_ver_db];
	}
	public static function verAdd($sid){
		$info = Design::find($sid);
		$json = Design::getDesignJson($sid);
		$ver = [
			'sid'=>$sid,
			's_bill'=>unit::OrderNumber(),
			's_name'=>$json['name'],
			's_db'=>$json['name_db'],
			's_list'=>$info['s_list'],
			's_search'=>$info['s_search'],
			's_fun_ver'=>'',
			's_field'=>$info['s_field'],
			'add_user'=>1,
			'status'=>1,
			'add_time'=>time()
		];
		$id  = Design::insertVer($ver);
		$map[] = ['id','<>',$id];
		$map[] = ['sid','=',$sid];
		Design::updateVerWhere($map,['status'=>0]);
		return self::ver($sid);
		
	}
}