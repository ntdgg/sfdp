<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp Api接口类
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
  *+------------------
  */
namespace sfdp;

use think\Request;
use think\Db;

use sfdp\service\Control;//引入核心控制器

use sfdp\fun\SfdpUnit;

use sfdp\lib\unit;

define('SFDP_Ver', '7.0.0' );

define('BEASE_SFDPURL', realpath ( dirname ( __FILE__ ) ) );

define('ROOT_PATH',dirname(dirname(__DIR__) . DIRECTORY_SEPARATOR, 1) . DIRECTORY_SEPARATOR . 'sfdp' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'template');

class Api
{
	function __construct($sid='') {
		$ginfo = unit::getuserinfo();
		if($ginfo==-1){
			echo 'Access Error!';exit;
		}
		if($sid==''){
			$sid = input('sid');
		}
   }
   /**
	  * Sfdp 5.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 sfdp\server\Control 的核心适配器进行API接口的调用
	  */
	 public function sfdpApi($act='list',$sid='',$map = null){
         if($act=='scan'){
             return Control::api($act,input('id'));
         }
         if($act=='sign'){
             return Control::api($act,input('id'));
         }
         if($act=='field'){
             return Control::api($act,$sid);
         }
		if($act=='list' || $act=='fun' || $act=='create' || $act=='listData' ){
			$s_type = input('s_type');
            if ($act=='listData'){
                return Control::api($act,$s_type,$map);
            }else{
                return Control::api($act,$s_type);
            }
		}
		if($act=='node'){
			$node = input('node');
			$data = ['sid'=>$sid,'node'=>$node];
			return Control::api($act,$data);
		}
		if(in_array($act,['desc','center','script','fieldkeyBuild','fieldkey','mysql','ui','fix','fix2','deldb','custom','customCount','customSave','customSearch','customSearch','customAccess','customData','customOrder','customShow','customUserConfig'])){
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
        if($act=='fieldkeyDel'){
            $data = ['sid'=>input('sid'),'id'=>input('id')];;
            return Control::api($act,$data);
        }
		
	}
	/**
	  * Sfdp 5.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 sfdp\server\Control 的核心适配器进行API接口的调用
	  */
	public function sfdpCurd($act='index',$sid='',$bid='',$search=[]){
        if($act=='info'){
            return Control::curd($act,$sid,'');
        }
		if($act=='index2'){
			$data = input('post.');
			return Control::curd($act,$sid,$data);
		}
		if($act=='index'){
			$data = input('post.');
			return Control::curd($act,$sid,$data);
		}
		if($act=='add'){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::curd($act,$sid,$data);
			 }else{
               return Control::curd($act,$sid,'');
			 }
		}
        if($act=='show'){
           return Control::curd('add',$sid,'');
        }
		if($act=='edit'){
			if (unit::is_post()) {
				$data = input('post.');
				return Control::curd($act,$sid,$data,$bid);
			 }else{
               return Control::curd($act,$sid,'',$bid);
			 }
		}
		if($act=='view'){
			return Control::curd($act,$sid,$bid);
		}
		if($act=='del'){		
             return Control::curd($act,$sid,'',$bid);
		}
		if($act=='GetData'){
            if(empty($search)){
                $data = input('post.');
            }else{
                $data = $search;
            }
			return Control::curd($act,$sid,$data);
		}
        if($act=='Data'){
            return Control::curd($act,$sid,$bid);
        }
        if($act=='UserConfig'){
            return Control::curd($act,$sid);
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
