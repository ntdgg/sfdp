<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V3.0
 *+------------------
 * Copyright (c) 2018~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace sfdp;

//定义

function int_config(){
	return [
		//定义用户基础信息  [type=>['表名','主键'，'getfield','field','searchwhere']]
		'int_user'=>['user'=>['user','id','username','id as id,username as username','username'],'role'=>['role','id','name','id as id,name as username','name']],
		//定义数据表前缀
		'int_db_prefix'=> 'wf_'
	];
}

function tab($step = 1, $string = ' ', $size = 4)
{
    return str_repeat($string, $size * $step);
}
