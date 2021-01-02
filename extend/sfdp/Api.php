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
		if($act=='list'){
			return Control::Api($act);
		}
		if($act=='desc'){
			return Control::Api($act,$sid);
		}
		if($act=='save'){
			$data = input('post.');
			return Control::Api($act,$data);
		}
		
	}
   
	/*构建表单目录*/
	static function sdfp_menu(){
		return  SfdpUnit::Bmenu();
	}
	/*动态生成列表*/
	public function lists($sid)
	{
		$map = SfdpUnit::Bsearch(input('post.'));
		$data = DescDb::getListData($sid,$map);
		$config = [
			'g_js'=>$this->topconfig,
			'sid' =>$sid,
			'field'=>$data['field']['fieldname'],
			'search' =>$data['field']['search'],
			'title' =>$data['title'],
			'load_file' =>$data['field']['load_file'],
		];
		return view($this->patch.'/index.html',['config'=>$config,'list'=>$data['list']]);
	}
	/*动态生成表单*/
	public function add($sid)
	{
		$data = DescDb::getAddData($sid);
		$config = [
			'g_js'=>$this->topconfig,
			'fun' =>$data['fun'],
			'load_file' =>$data['load_file'],
		];
		return view($this->patch.'/edit.html',['config'=>$config,'data'=>$data['info']['s_field']]);
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
	/*删除备份数据库*/
	public function sfdp_deldb($sid){
		 $bulid = new BuildTable();
		 $json = DescDb::getDesignJson($sid);
		 $ret = $bulid->delDbbak($json['name_db']);
		 if($ret['code']==0){
			 DescDb::saveDesc(['s_db_bak'=>0,'id'=>$sid],'update');
		 }
		 return json($ret);
	}
	/*部署生成*/
	public function sfdp_fix($sid){
		$bulid = new BuildTable();
		$info = DescDb::getDesign($sid);
		$json = DescDb::getDesignJson($sid);
		if($info['s_list']=='[]'){
			return json(['code'=>1,'msg'=>'Error,对不起您没有配置列表选项']);
		}
		$ret = $bulid->hasDbbak($json['name_db']);
		if($ret['code']==1){
			DescDb::saveDesc(['s_db_bak'=>1,'id'=>$sid],'update');
			 return json($ret);
		 }
		//添加并返回
		$tablefield = ViewDb::verAdd($sid);
		$ret = $bulid->Btable($json['name_db'],$tablefield['db']);
		 DescDb::saveDesc(['s_db_bak'=>1,'s_design'=>2,'id'=>$sid],'update');
		return json(['code'=>0]);
	}
	/*脚本功能*/
	public function sfdp_script($sid){
		  return view($this->patch.'/sfdp_script.html',['sid'=>$sid,'info'=>ScriptDb::script($sid)]);
	}
	/*元素Ui功能*/
	public function sfdp_ui($sid){
		$info = DescDb::getDesign($sid);
		if($info['s_design']<>2){
			echo "<script language='javascript'>alert('Err,请先设计并部署！！'); top.location.reload();</script>";
			exit;
		}
		$json = ViewDb::ver($sid);
		return view($this->patch.'/sfdp_ui.html',['sid'=>$sid,'ui'=>$json['db']]);
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
