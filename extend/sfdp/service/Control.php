<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V5.0
 *+------------------
 * Copyright (c) 2018~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace sfdp\service;

use think\Db;

use sfdp\db\DescDb;
use sfdp\db\ViewDb;
use sfdp\db\ScriptDb;
use sfdp\db\FunctionDb;

use sfdp\fun\BuildFun;
use sfdp\fun\SfdpUnit;
use sfdp\fun\BuildTable;

class Control{
	
	static function api($act,$sid=''){
		//获取流程信息
		if($act =='list'){
			$list = Db::name('sfdp_design')->order('id desc')->select();
			return view(ROOT_PATH.'/sfdp.html',['list'=>$list,'patch'=>ROOT_PATH]);
		}
		if($act =='fun'){
			$list = Db::name('sfdp_function')->order('id desc')->select();
			return view(ROOT_PATH.'/sfdp_fun.html',['list'=>$list]);
		}
		if($act =='desc'){
			 $info = DescDb::getDesign($sid);
			 return view(ROOT_PATH.'/sfdp_desc.html',['json'=>$info['s_field'],'fid'=>$info['id'],'look'=>$info['s_look']]);
		}
		if($act =='script'){
			return view(ROOT_PATH.'/sfdp_script.html',['sid'=>$sid,'info'=>ScriptDb::script($sid)]);
		}
		if($act =='ui'){
			$info = DescDb::getDesign($sid);
			if($info['s_design']<>2){
				echo "<script language='javascript'>alert('Err,请先设计并部署！！'); top.location.reload();</script>";
				exit;
			}
			$json = ViewDb::ver($sid);
			return view(ROOT_PATH.'/sfdp_ui.html',['sid'=>$sid,'ui'=>$json['db']]);
		}
		if($act =='save'){
			return json(DescDb::saveDesc($sid,'save'));
		}
		if($act =='deldb'){
			 $json = DescDb::getDesignJson($sid);
			 $ret = BuildTable::delDbbak($json['name_db']);
			 if($ret['code']==0){
				 DescDb::saveDesc(['s_db_bak'=>0,'id'=>$sid],'update');
			 }
			 return json($ret);
		}
		if($act =='fix'){
			$info = DescDb::getDesign($sid);
			$json = DescDb::getDesignJson($sid);
			if($info['s_list']=='[]'){
				return json(['code'=>1,'msg'=>'Error,对不起您没有配置列表选项']);
			}
			$ret =  BuildTable::hasDbbak($json['name_db']);
			if($ret['code']==1){
				DescDb::saveDesc(['s_db_bak'=>1,'id'=>$sid],'update');
				 return json($ret);
			 }
			//添加并返回
			$tablefield = ViewDb::verAdd($sid);
			$ret = BuildTable::Btable($json['name_db'],$tablefield['db']);
			 DescDb::saveDesc(['s_db_bak'=>1,'s_design'=>2,'id'=>$sid],'update');
			return json(['code'=>0]);
		}
        return $act.'参数出错';
	}
	static function curd($act,$sid='',$data='',$g_js){
		
		if($act =='index'){
			$map = SfdpUnit::Bsearch($data);
			$list = DescDb::getListData($sid,$map);
			$config = [
				'g_js'=>$g_js,
				'sid' =>$sid,
				'field'=>$list['field']['fieldname'],
				'search' =>$list['field']['search'],
				'title' =>$list['title'],
				'load_file' =>$list['field']['load_file'],
			];
		return view(ROOT_PATH.'/index.html',['config'=>$config,'list'=>$list['list']]);
		}
		if($act =='add'){
			if($data !=''){
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
			$data = DescDb::getAddData($sid);
			$config = [
				'g_js'=>$g_js,
				'fun' =>$data['fun'],
				'load_file' =>$data['load_file'],
			];
			return view(ROOT_PATH.'/edit.html',['config'=>$config,'data'=>$data['info']['s_field']]);
		}
		
		
	}
	
	
	
	
	
	
}