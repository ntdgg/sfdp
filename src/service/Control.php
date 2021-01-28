<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 核心驱动类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\service;

use sfdp\adaptive\Design;
use sfdp\adaptive\View;
use sfdp\adaptive\Script;
use sfdp\adaptive\Functions;
use sfdp\adaptive\Data;
use sfdp\adaptive\Common;

use sfdp\fun\BuildFun;
use sfdp\fun\SfdpUnit;
use sfdp\fun\BuildTable;
use sfdp\lib\lib;

use sfdp\lib\unit;

class Control{
	
	static function api($act,$sid=''){
		//获取流程信息
		if($act =='list'){
			$list = Design::select();
			return lib::index($list);
		}
		if($act =='fun'){
			$list = Functions::select();
			
			return lib::fun($list);
		}
		if($act =='desc'){
			 $info = Design::find($sid);
			 return lib::desc($info['s_field'],$info['id'],$info['s_look']);
			 
			 return view(ROOT_PATH.'/sfdp_desc.html',['json'=>$info['s_field'],'fid'=>$info['id'],'look'=>$info['s_look']]);
		}
		if($act =='script'){
			if($sid !='' && is_array($sid)){
				$bill = Script::scriptSave($sid);
				BuildFun::Bfun($sid['function'],$bill);
				$urls= unit::gconfig('url');
				$action = $urls['api'].'?act=script&sid='.$sid['sid'];
				echo "<script language='javascript'>alert('Success,脚本生成成功！'); location.assign('".$action."');</script>";exit;
			}
			
			return lib::script(Script::script($sid),$sid);
		}
		if($act =='ui'){
			$info = Design::find($sid);
			if($info['s_design']<>2){
				$action = $urls['api'].'?act=ui&sid='.$sid;
				echo "<script language='javascript'>alert('Err,请先设计并部署！！'); location.assign('".$action."');</script>";
				exit;
			}
			$json = View::ver($sid);
			return lib::ui($json['db']);
		}
		if($act =='save'){
			return json(Design::saveDesc($sid,'save'));
		}
		if($act =='deldb'){
			 $json = Design::getDesignJson($sid);
			 $ret = BuildTable::delDbbak($json['name_db']);
			 if($ret['code']==0){
				 Design::saveDesc(['s_db_bak'=>0,'id'=>$sid],'update');
			 }
			 return json($ret);
		}
		if($act =='fix'){
			$info = Design::find($sid);
			$json = Design::getDesignJson($sid);
			if($info['s_list']=='[]'){
				return json(['code'=>1,'msg'=>'Error,对不起您没有配置列表选项']);
			}
			$ret =  BuildTable::hasDbbak($json['name_db']);
			if($ret['code']==1){
				Design::saveDesc(['s_db_bak'=>1,'id'=>$sid],'update');
				 return json($ret);
			 }
			//添加并返回
			$tablefield = View::verAdd($sid);
			$ret = BuildTable::Btable($json['name_db'],$tablefield['db']);
			 Design::saveDesc(['s_db_bak'=>1,'s_design'=>2,'id'=>$sid],'update');
			return json(['code'=>0]);
		}
		if($act=='fun_save'){
			return Functions::functionSave($sid);
			
		}
		if($act=='create'){
			$id = Design::saveDesc('','create');
			return json(['code'=>0]);
		}
		if($act=='node'){
			
			$className = unit::gconfig('node_action');
			if(!class_exists($className)){
				return 'Sorry,未找到node_action类，请先配置~';
			}
			$Node = (new $className())->SaveNode(Design::descVerTodata($sid['sid']),$sid['node']);//获取目录节点信息	
			if($Node['code']==0){
				return json(['code'=>0]);
			}else{
				return json(['code'=>1,'msg'=>$Node['msg']]);
			}
			
		}
        return $act.'参数出错';
	}
	static function curd($act,$sid='',$data='',$g_js=''){
		
		if($act =='index'){
			$map = SfdpUnit::Bsearch($data);
			$list = Data::getListData($sid,$map);
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
		if($act =='GetData'){
			$map = SfdpUnit::Bsearch($data);
			$list = Data::getListData($sid,$map);
			$jsondata = [];
			foreach($list['list'] as $k=>$v){
				$list['list'][$k]['url'] = '<a onClick=commonfun.openfullpage("查看","'.url('/index/sfdp/sfdpCurd',['act'=>'view','sid'=>$sid,'bid'=>$v['id']]).'")	class="btn  radius size-S">查看</a>';
				$jsondata[$k] = array_values($list['list'][$k]);
			}
			return json(['data'=>$jsondata]);
			
		}
		if($act=='view'){
			$info = Data::getViewData($sid,$data);
			return view(ROOT_PATH.'/view.html',['info'=>$info['info']]);
		}
		if($act =='add'){
			if($data !=''){
				Data::add($sid,$data);
				return json(['code'=>0]);
			}
			$data = Data::getAddData($sid);
			$config = [
				'g_js'=>$g_js,
				'fun' =>$data['fun'],
				'load_file' =>$data['load_file'],
			];
			return view(ROOT_PATH.'/edit.html',['config'=>$config,'data'=>$data['info']['s_field']]);
		}
	}
	static function fapi($post){
		$key_name = [];
		$key_val = [];
		foreach($post as $k=>$v){
			if($k<>'fun'){
				$key_name[] = '@'.$k;
				$key_val[] = $v;
			}
		}
		$sql = Functions::findWhere([['fun_name','=',$post['fun']]]);;
		$new_sql=str_replace($key_name,$key_val,$sql['function']);
		return Common::query($new_sql);
	}
}