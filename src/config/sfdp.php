<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V3.0
 *+------------------
 * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
return [
	'int_db_prefix'=> 'wf_',//定义数据表前缀
	'int_user_name'=> 'username',//定义用户名称
	'int_user_id'=> 'uid',//定义用户id
	'int_user_role'=> 'role',//定义用户角色
	'black_table'=>['sfdp_design','sfdp_design_ver','sfdp_function','sfdp_script'],//黑名单表，防止重复
	'db_namespace'=>'',
	'db_mode'=>1,
	'static_url'=>'/static/sfdp/',//资源目录
	'gateway_mode' =>1,//1,默认使用Tp的助手函数
	'gateway_action' =>'',//自定义方法返回数据 命名空间 中的GetUserInfo
	'url' => [
		'api'=>"/index/sfdp/sfdpApi.html",
	],
	'return_mode' => 1,//1、系统模式  2、二次开发模式
	'node_mode' => 1,//模式
	'node_data'=>[
		'html'=>'<option value="1">首页</option><option value="2">后台</option>',
	],//黑名单表，防止重复
	'node_action'=>'\\Node',//获取目录方法，用于目录挂载  SaveNode保存接口  GetNode获取节点接口
	'upload_file' => '/gadmin/sys/upload',//附件上传接口
];
