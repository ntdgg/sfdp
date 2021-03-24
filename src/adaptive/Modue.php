<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 数据类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\adaptive;


use sfdp\fun\SfdpUnit;
use sfdp\lib\unit;

class Modue{
	
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('db_mode')==1){
			$className = '\\sfdp\\custom\\AdapteeModue';
		}else{
			$className = unit::gconfig('db_namespace').'AdapteeModue';
		}
		$this->mode = new $className();
    }
	static function saveWhere($where,$data)
    {
		return (new Modue())->mode->saveWhere($where,$data);
    }
	
	/**
	 * 添加
	 */
	static function add($varInfo,$btn){
		$data = [
			'sid'=>$varInfo['ver']['id'],
			'title'=>$varInfo['ver']['s_name'],
			'dbtable'=>$varInfo['ver']['s_db'],
			'btn'=> implode($btn,',')
		];
		return (new Modue())->mode->add($data);		
	}
	/**
	 * 添加
	 */
	static function find($sid){
		return (new Modue())->mode->find($sid);		
	}
	static function findWhere($map){
		return (new Modue())->mode->findWhere($map);		
	}
	

	
	
	
	
}