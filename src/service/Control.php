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
		$urls= unit::gconfig('url');
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
		}
		if($act =='script'){
			if($sid !='' && is_array($sid)){
				$bill = Script::scriptSave($sid);
				BuildFun::Bfun($sid['function'],$bill);
				$action = $urls['api'].'?act=script&sid='.$sid['sid'];
				echo "<script language='javascript'>alert('Success,脚本生成成功！'); location.assign('".$action."');</script>";exit;
			}
			return lib::script(Script::script($sid),$sid);
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
			$tablefield = View::verAdd($sid);
			$all = json_decode($tablefield['all'],true);
			$ret2 = BuildTable::Btable($json['name_db'],$tablefield['db'],$all['tpfd_btn'],$all['name']);
			if($ret2['code']==-1){
				return json($ret2);
			}
			$ret = Design::saveDesc(['s_db_bak'=>1,'s_design'=>2,'id'=>$sid],'update');
			return json(['code'=>0]);
		}
		if($act=='fun_save'){
			return Functions::save($sid);
		}
		if($act=='fun_update'){
			$ret =  Functions::update($sid);
			if($ret){
				return json(['code'=>0,'msg'=>'操作成功！']);
			}else{
				return json(['code'=>-1,'msg'=>'更新出错']);
			}
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
			$ver = Design::findVerWhere([['status','=',1],['sid','=',$sid['sid']]]);//取得版本ID
			$Node = (new $className())->SaveNode(Design::descVerTodata($ver['id']),$sid['node']);//获取目录节点信息	
			if($Node['code']==0){
				return json(['code'=>0]);
			}else{
				return json(['code'=>1,'msg'=>$Node['msg']]);
			}
		}
		if($act =='customSave'){
			$ret = View::SaveVer($sid['sid'],$sid['data']);
			if($ret){
				return json(['code'=>0,'msg'=>'保存成功']);
			}else{
				return json(['code'=>1,'msg'=>'保存失败']);
			}
		}
		if($act =='custom'){
			$info = Design::find($sid);
			if($info['s_design']<>2){
				echo "<script language='javascript'>alert('Err,请先设计并部署！！'); </script>";
				exit;
			}
			$json = View::ver($sid);
			$listtrue ='';
			$list = '';
			  foreach($json['db'] as $k=>$v){
				  if($v['tpfd_list']=='yes'){
					  $list .=  '<li class="ui-state-default" data-id="'.$v['tpfd_id'].'">'.$v['tpfd_db'].'('.$v['tpfd_name'].')</li>'; 
				  }else{
					  $listtrue  .= ' <li class="ui-state-highlight " data-id="'.$v['tpfd_id'].'">'.$v['tpfd_db'].'('.$v['tpfd_name'].')</li>'; 
				  }
			  }
			return lib::custom($sid,$list,$listtrue);
		}
        return $act.'参数出错';
	}
	/**
	 *获取按钮数据
	 */
	static function btnArray($btnArray,$sid){
       $btns ='';
	    if((in_array('add',$btnArray))){
			$url = url('index/sfdp/sfdpCurd',['act'=>'add','sid'=>$sid]);
			$btns .= '<a class="btn btn-primary radius" onclick=layer_show("新增","'.$url.'","850","500") ><i class="Hui-iconfont-add Hui-iconfont"></i> 新增</a> ';
		}
	   if((in_array('Edit',$btnArray))){
			$btns .= '<a onClick="edit('.$sid.')" class="btn btn-success radius"><i class="Hui-iconfont Hui-iconfont-edit"></i>修改</a> ';
		}
	   if(in_array('Del',$btnArray)){
			$btns .= ' <a onClick="del('.$sid.')"	class="btn btn-danger radius"><i class="Hui-iconfont Hui-iconfont-del3"></i>删除</a> ';
		}
	   return $btns;
    }
	static function curd($act,$sid,$data='',$g_js='',$bid=''){
		
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
			$btns = self::btnArray($list['field']['btn'],$sid);
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/index.html',['btn'=>$btns,'config'=>$config,'list'=>$list['list']]);
				}else{
				return ['config'=>$config,'list'=>$list['list'],'btn'=>$list['field']['btn']];
			}
		}
		
		if($act =='GetData'){
			$map = SfdpUnit::Bsearch($data);
			$list = Data::getListData($sid,$map,$data['page'],$data['limit']);
			$jsondata = [];
			$btnArray = $list['field']['btn'];
			$tablename = $list['field']['db_name'];
			$stv = [
				-1=>'<span class="label label-danger radius" >退回修改</span>',0=>'<span class="label radius">保存中</span>',1=>'<span class="label radius" >流程中</span>',2=>'<span class="label label-success radius" >审核通过</span>'
			];
			foreach($list['list'] as $k=>$v){
				
				$list['list'][$k]['id'] = '<input type="checkbox" value="'.$v['id'].'/'.$v['status'].'/'.$tablename.'" name="ids">';
				
				$list['list'][$k]['status'] =   $stv[$v['status']] ?? 'ERR';
				$list['list'][$k]['url'] = '<a onClick=commonfun.openfullpage("查看","'.url('/index/sfdp/sfdpCurd',['act'=>'view','sid'=>$sid,'bid'=>$v['id']]).'")	class="btn  radius size-S">查看</a>';
				$jsondata[$k] = array_values($list['list'][$k]);
			}
			if(unit::gconfig('return_mode')==1){
				
				return json(['data'=>$jsondata,'recordsFiltered'=>$list['count'],'recordsTotal'=>$list['count']]);
				}else{
				return ['data'=>$jsondata,'list'=>$list,'count'=>$list['count']];
			}
		}
		if($act=='view'){
			$info = Data::getViewData($sid,$data);
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/view.html',['info'=>$info['info']]);
				}else{
				return ['info'=>$info['info']];
			}
			
		}
		if($act =='edit'){
			if($data !=''){
				Data::edit($sid,$data,$bid);
				return json(['code'=>0]);
			}
			
			$data = Data::getEditData($sid,$bid);
			$viewdata = $data['data'];
			$config = [
				'g_js'=>$g_js,
				'fun' =>$viewdata['fun'],
				'load_file' =>$viewdata['load_file'],
				'upload_file'=>unit::gconfig('upload_file')
			];
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/edit.html',['showtype'=>'edit','config'=>$config,'data'=>$data['info']]);
				}else{
				return ['config'=>$config,'data'=>$data['info']];
			}
		}
		if($act =='del'){	
			Data::del($sid,$bid);
			return json(['code'=>0]);	
		}
		if($act =='add'){
			if($data !=''){
				Data::add($sid,$data);
				return json(['code'=>0]);
			}
			$data = Design::getAddData($sid);
			$config = [
				'g_js'=>$g_js,
				'fun' =>$data['fun'],
				'load_file' =>$data['load_file'],
				'upload_file'=>unit::gconfig('upload_file')
			];
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/edit.html',['showtype'=>'add','config'=>$config,'data'=>$data['info']['s_field']]);
				}else{
				return ['config'=>$config,'data'=>$data['info']['s_field']];
			}
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