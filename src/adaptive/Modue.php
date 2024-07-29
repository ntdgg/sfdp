<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 数据类
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
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
        $list_data = self::getConcatenatedStrings($varInfo['db']);
        $ver = json_decode($varInfo['ver']['s_field'],true);
		$data = [
			'sid'=>$varInfo['ver']['id'],
			'title'=>$varInfo['ver']['s_name'],
			'dbtable'=>$varInfo['ver']['s_db'],
            'field_name'=>$list_data[0] ?? '',
            'field'=>$list_data[1] ?? '',
            'is_delete'=>$ver['tpfd_del'] ?? 1,
            'is_saas'=>$ver['tpfd_saas'] ?? 1,
			'btn'=> implode(',',$btn)
		];
		return (new Modue())->mode->add($data);		
	}
    static function getConcatenatedStrings($array) {
        // 获取数组的长度，最多为4
        $length = min(count($array), 4);
        // 初始化两个空数组用于存储 `name` 和 `field` 的值
        $names = [];
        $fields = [];
        // 循环取最多4个元素
        for ($i = 0; $i < $length; $i++) {
            $names[] = $array[$i]['tpfd_name'];
            $fields[] = $array[$i]['tpfd_db'];
        }
        // 拼接成所需的字符串格式
        $namesString = implode(',', $names);
        $fieldsString = implode(',', $fields);
        return [$namesString,$fieldsString];
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