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
use sfdp\db\DescDb;
use sfdp\db\ViewDb;
use sfdp\db\ScriptDb;
use sfdp\db\FunctionDb;

use sfdp\fun\BuildFun;
use sfdp\fun\SfdpUnit;
use sfdp\fun\BuildTable;

use sfdp\lib\unit;
define('FILE_PATH', realpath ( dirname ( __FILE__ ) ) );
define('ROOT_PATH',\Env::get('root_path'). 'extend/sfdp/template' );

require_once FILE_PATH . '/config/config.php';

class Api
{
	public $patch = '';
	public $topconfig = '';
	function __construct() {
		$int_config = int_config();
		$sid = input('sid') ?? 0;
		$g_uid = input('session.'.$int_config['int_user_id']) ?? '9999';
		$g_username = input('session.'.$int_config['int_user_name']) ?? '"admin"';
		$g_role = input('session.'.$int_config['int_user_role']) ?? '9999';
		$this->topconfig = 
		'<script>
		var g_uid='.$g_uid.';
		var g_role='.$g_role.';
		var g_username='.$g_username.';
		var g_sid='.$sid.';
		</script>';
		$this->patch =  ROOT_PATH;
   }
   /**
	  * Tpflow 5.0统一接口流程审批接口
	  * @param string $act 调用接口方法
	  * 调用 tpflow\adaptive\Control 的核心适配器进行API接口的调用
	  */
	 public function sfdpApi($act='list',$sid=''){
		if($act=='list' || $act=='fun' ){
			return Control::api($act);
		}
		if($act=='desc' || $act=='script' || $act=='ui' || $act=='fix' || $act=='deldb'){
			return Control::api($act,$sid);
		}
		if($act=='save'){
			$data = input('post.');
			return Control::api($act,$data);
		}
	}
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
		if($act=='GetData'){
			$data = input('post.');
			$map = SfdpUnit::Bsearch($data);
			$list = DescDb::getListData($sid,$map);
			$data = [];
			foreach($list['list'] as $k=>$v){
				$list['list'][$k]['url'] = '<a onClick=commonfun.openfullpage("查看","'.url('/index/sfdp/sfdp_view',['sid'=>$sid,'bid'=>$v['id']]).'")	class="btn  radius size-S">查看</a>';
				$data[$k] = array_values($list['list'][$k]);
				
				
			}
			
			return json(['data'=>$data]);
		}
		
		
		
		
	}
	
   
	/*构建表单目录*/
	static function sdfp_menu(){
		return  SfdpUnit::Bmenu();
	}
	
	
	/*创建一个新得表单*/
	public function sfdp_create(){
		$id = DescDb::saveDesc('','create');
		return json(['code'=>0]);
	}
	
	/*函数列表*/
	public function sfdp_fun($sid=''){
		$data = Db::name('sfdp_function')->paginate('10');
		return view($this->patch.'/sfdp_fun.html',['list'=>$data]);
	}
	/*脚本保存*/
	public function sfdp_script_save(){
		$data = input('post.');
		$bill = ScriptDb::scriptSave($data);
		$bulid = new BuildFun();
		$bulid->Bfun($data['function'],$bill);
		echo "<script language='javascript'>alert('Success,脚本生成成功！'); top.location.reload();</script>";
	}
	/*函数保存*/
	public function sfdp_fun_save(){
		$data = input('post.');
		return FunctionDb::functionSave($data);
	}
	/*函数方法*/
	public function get_function_val(){
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
	public function saveadd($sid){
		$data = input('post.');
		foreach($data as $k=>$v){
			if(is_array($v)){
				$data[$k] = implode(",", $v);
			}
		}
		$table = $data['name_db'];
		unset($data['name_db']);
		unset($data['tpfd_check']);
		db($table)->insertGetId($data);
		return json(['code'=>0]);
	}
	public function sfdp_view($sid,$bid){
		$data = DescDb::getViewData($sid,$bid);
		return view($this->patch.'/view.html',['info'=>$data['info']]);
	}
}
