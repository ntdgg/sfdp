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
	public static function SaveVer($sid,$data){
		$json = Design::findVerWhere([['status','=',1],['sid','=',$sid]]);
		$field = json_decode($json['s_field'],true);
		$new_list =[];
		foreach($field['list'] as $k=>$v){
			foreach($v['data'] as $k2=>$v2){
				if(in_array($v2['tpfd_id'],$data)){
					$field['list'][$k]['data'][$k2]['tpfd_list'] ='yes';
					$new_list[] = $v2;
				}else{
					$field['list'][$k]['data'][$k2]['tpfd_list'] ='no';
				}
			}
		}
		$s_field = json_encode($field);//排序后的新列表字段数据
		$s_list =json_encode($new_list);
		return Design::updateVer(['id'=>$json['id'],'s_field'=>$s_field,'s_list'=>$s_list]);
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