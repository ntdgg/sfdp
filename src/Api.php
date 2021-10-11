<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp Api接口类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
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

define('BEASE_SFDPURL', realpath ( dirname ( __FILE__ ) ) );

define('ROOT_PATH',dirname(dirname(__DIR__) . DIRECTORY_SEPARATOR, 1) . DIRECTORY_SEPARATOR . 'sfdp' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'template');

class Api
{
	public $topconfig = '';
	function __construct($sid='') {
		$ginfo = unit::getuserinfo();
		if($ginfo==-1){
			echo 'Access Error!';exit;
		}
		if($sid==''){
			$sid = input('sid');
		}
		$this->topconfig = 
		'<script>
		var g_uid='.$ginfo['uid'].';
		var g_role='.$ginfo['role'].';
		var g_username="'.$ginfo['username'].'";
		var g_sid='.$sid.';
		</script>';
   }
   /**
	  * Sfdp 5.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 sfdp\server\Control 的核心适配器进行API接口的调用
	  */
	 public function sfdpApi($act='list',$sid=''){
		if($act=='list' || $act=='fun' || $act=='create' || $act=='listData' ){
			$s_type = input('s_type');
			return Control::api($act,$s_type);
		}
		if($act=='node'){
			$node = input('node');
			$data = ['sid'=>$sid,'node'=>$node];
			return Control::api($act,$data);
		}
		if(in_array($act,['desc','script','ui','fix','fix2','deldb','custom','customSave','customSearch','customSearch','customAccess','customOrder','customShow'])){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::api($act,$data);
			 }else{
               return Control::api($act,$sid);
			 }
		}
		if($act=='save' || $act=='fun_save'){
			$data = input('post.');
			return Control::api($act,$data);
		}
         if($act=='field'){
             $data = input('post.');
             $data['sid'] = $sid;
             return Control::api($act,$data);
         }
		if($act=='fun_update'){
			$data = ['id'=>input('id'),'status'=>input('status')];;
			return Control::api($act,$data);
		}
		if($act=='btable'){
			$data = input('post.');
			return Control::api($act,$data);
		}
		
	}
	/**
	  * Sfdp 5.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 sfdp\server\Control 的核心适配器进行API接口的调用
	  */
	public function sfdpCurd($act='index',$sid='',$bid=''){
		if($act=='index2'){
			$data = input('post.');
			return Control::curd($act,$sid,$data,$this->topconfig);
		}
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
		if($act=='edit'){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::curd($act,$sid,$data,$this->topconfig,$bid);
			 }else{
               return Control::curd($act,$sid,'',$this->topconfig,$bid);
			 }
		}
		if($act=='view'){
			return Control::curd($act,$sid,$bid);
		}
		if($act=='del'){		
             return Control::curd($act,$sid,'',$this->topconfig,$bid);
		}
		if($act=='GetData'){
			$data = input('post.');
			return Control::curd($act,$sid,$data,$this->topconfig);
		}
        if($act=='Data'){
            return Control::curd($act,$sid,$bid);
        }
		
	}
	/**
	  * Sfdp 5.0 内部目录调用方法
	  * 调用 sfdp\server\Control 的核心适配器进行API接口的调用
	  */
	static function sdfp_menu(){
		return  SfdpUnit::Bmenu();
	}
	
	/**
	  * Sfdp 5.0 函数调用API
	  * 调用 sfdp\server\Control 的核心适配器进行API接口的调用
	  */
	public function fApi(){
		$post = input('post.');
		$json = Control::fApi($post);;
		return json($json);
	}

}
