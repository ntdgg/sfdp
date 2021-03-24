<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 工具类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\lib;


class unit{
	
	/**
	 * 判断是否是POST
	 *
	 **/
	public static function is_post()
	{
	   return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='POST';	
	}
	/**
	 * 加载自定义事务驱动文件
	 *
	 * @param  string $class 类
	 * @param  int $id 单据对应的ID编号
	 * @param  int  $run_id 运行中的流程ID
	 * @param  array $data 步骤类
	 */
	public static function LoadClass($class,$id,$run_id='',$data='') {
		$className = unit::gconfig('wf_work_namespace').$class;
		if(!class_exists($className)){
			return -1;
		}
		return new $className($id,$run_id,$data);
	} 
	/**
	* 获取定义的信息
	* @param string $key
	**/
	public static function getuserinfo($key='') {
		if(unit::gconfig('gateway_mode')==1){
			$user_info = [
				'uid'=>session(self::gconfig('int_user_id')),
				'username'=>session(self::gconfig('int_user_name')),
				'role'=>session(self::gconfig('int_user_role')),
			];
		}else{
			$className = unit::gconfig('gateway_action');
			if(!class_exists($className)){
				return -1;
			}
			$user_info = (new $className())->GetUserInfo();;
		}
		if($user_info['uid']==''||$user_info['role']==''||$user_info['username']==''){
			return -1;
		}
		if($key==''){
			return $user_info;
		}else{
			return $user_info[$key] ?? '';
		}
		
	}
	/**
	 * 根据键值加载全局配置文件
	 *
	 * @param string $key 键值
	 */
	public static function gconfig($key) {
		$file = dirname(dirname(__DIR__) . DIRECTORY_SEPARATOR, 4) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'sfdp.php';
		if(!file_exists($file)){
			echo 'sorry,config no find!';exit;
		}
		$ret =require($file);
		return $ret[$key] ?? '';
	}
	/**
	 * 缩进设置
	 *
	 * @param string $step 步骤
	 * @param string $string 字符串
	 * @param string $size 长度
	 */
	static function tab($step = 1, $string = ' ', $size = 4)
	{
		return str_repeat($string, $size * $step);
	}
	/**
	 * 根据日期编码生成
	 *
	 */
	static function OrderNumber()
	{
		return "D" . date("Ymd").rand(1000,9999);;
	}
}

