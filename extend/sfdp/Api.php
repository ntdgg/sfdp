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

use think\Request;
use think\Db;

use sfdp\service\Control;//引入核心控制器

use sfdp\fun\SfdpUnit;

use sfdp\lib\unit;
define('FILE_PATH', realpath ( dirname ( __FILE__ ) ) );
define('ROOT_PATH',\Env::get('root_path'). 'extend/sfdp/template' );

class Api
{
	public $topconfig = '';
	function __construct() {
		$sid = input('sid') ?? 0;
		$g_uid = input('session.'.unit::gconfig('int_user_id')) ?? '9999';
		$g_username = input('session.'.unit::gconfig('int_user_name')) ?? '"admin"';
		$g_role = input('session.'.unit::gconfig('int_user_role')) ?? '9999';
		$this->topconfig = 
		'<script>
		var g_uid='.$g_uid.';
		var g_role='.$g_role.';
		var g_username='.$g_username.';
		var g_sid='.$sid.';
		</script>';
   }
   /**
	  * Sfdp 5.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 sfdp\server\Control 的核心适配器进行API接口的调用
	  */
	 public function sfdpApi($act='list',$sid=''){
		if($act=='list' || $act=='fun' || $act=='create'){
			return Control::api($act);
		}
		if($act=='desc' || $act=='script' || $act=='ui' || $act=='fix' || $act=='deldb'){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::api($act,$data);
			 }else{
               return Control::api($act,$sid);
			 }
		}
		if($act=='save' || $act=='fun_save' ){
			$data = input('post.');
			return Control::api($act,$data);
		}
	}
	/**
	  * Sfdp 5.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 sfdp\server\Control 的核心适配器进行API接口的调用
	  */
	public function sfdpCurd($act='index',$sid=''){
		if($act=='index'){
			$data = input('post.');
			return Control::curd($act,$sid,$data,$this->topconfig);
		}
		if($act=='add'){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::curd($act,$sid,$data,$this->topconfig);
			 }else{
               return Control::curd($act,$sid,'',$this->topconfig);
			 }
		}
		if($act=='view'){
			return Control::curd($act,$sid,input('bid'));
		}
		if($act=='GetData'){
			$data = input('post.');
			return Control::curd($act,$sid,$data,$this->topconfig);
		}
	}
	
   
	/*构建表单目录*/
	static function sdfp_menu(){
		return  SfdpUnit::Bmenu();
	}
	
	/*函数方法*/
	public function fApi(){
		$post = input('post.');
		$key_name = [];
		$key_val = [];
		foreach($post as $k=>$v){
			if($k<>'fun'){
				$key_name[] = '@'.$k;
				$key_val[] = $v;
			}
		}
		$sql = Db::name('sfdp_function')->where('fun_name',$post['fun'])->find();
		$new_sql=str_replace($key_name,$key_val,$sql['function']);
		$json = Db::query($new_sql);
		return json($json);
	}

}
