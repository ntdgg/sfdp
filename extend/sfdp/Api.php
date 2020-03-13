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
use think\view;
use sfdp\sfdp;
define ( 'FILE_PATH', realpath ( dirname ( __FILE__ ) ) );
define('APP_PATH',\Env::get('app_path') );
define('ROOT_PATH',\Env::get('root_path') );
define('DS',DIRECTORY_SEPARATOR);

require_once FILE_PATH . '/class/build.php';
require_once FILE_PATH . '/config/config.php';
require_once FILE_PATH . '/config/common.php';
require_once FILE_PATH . '/db/FbDb.php';
require_once FILE_PATH . '/class/BuildView.php';
require_once FILE_PATH . '/class/BuildFun.php';
require_once FILE_PATH . '/class/BuildTable.php';
require_once FILE_PATH . '/class/BuildController.php';

class Api
{
	/*构建表单目录*/
	static function sdfp_menu(){
		$menu  =  Db::name('sfdp_design_ver')->where('status',1)->select();
		$menu_html = '';
		foreach($menu as $k=>$v){
			$menu_html .='<li><a data-href="'.url('sfdp/list',['sid'=>$v['id']]).'" data-title="'.$v['s_name'].'">'.$v['s_name'].'</a></li>';
		}
		return $menu_html;
	}
	/*动态生成列表*/
	public function lists($sid)
	{
		$build = db('sfdp_design_ver')->find($sid);
		$field = json_decode($build['s_field'],true);
		$list_field = json_decode($build['s_list'],true);
		$topicid = ''; //变量赋值为空
			//用foreach 遍历下二维数组
			foreach($list_field as $key=>$vals){
				$topicid.=$vals['tpfd_db'].',';
				$topicname[$vals['tpfd_db']]=$vals['tpfd_db'];
			}
			$topicid = rtrim($topicid, ',');
		$list = db($field['name_db'])->field($topicid)->paginate('10');
		return view(env('root_path') . 'extend/sfdp/template/index.html',['sid'=>$sid,'list'=>$list,'field'=>$topicname]);
	}
	/*动态生成表单*/
	public function add($sid)
	{
		$json = db('sfdp_design_ver')->find($sid);
		if($json['s_fun_id']!=''){
			$fun = '<script src="\static/sfdp/user-defined/'.$json['s_fun_ver'].'.js"></script>';	
		}else{
			$fun = '';
		}
		return view(env('root_path') . 'extend/sfdp/template/edit.html',['fun'=>$fun,'data'=>$json['s_field']]);
	}
	public function sfdp($sid=''){
		$data = Db::name('sfdp_design')->paginate('10')->each(function($item, $key){
				$item['fix'] = Db::name('sfdp_design_ver')->where('sid',$item['id'])->order('id desc')->select();
				return $item;
			});
		
		//dump($data);
		return view(env('root_path') . 'extend/sfdp/template/sfdp.html',['list'=>$data]);
	}
	/**
     * 表单设计
     * @return mixed
     */
    public function sfdp_desc($sid)
    {
	   $info = db('sfdp_design')->find($sid);;
      return view(env('root_path') . 'extend/sfdp/template/sfdp_desc.html',['json'=>$info['s_field'],'fid'=>$info['id'],'look'=>$info['s_look']]);
    }
	public function sfdp_save(){
		$data = input('post.');
		$search = [];
		$list = [];
		$data['s_field'] = htmlspecialchars_decode($data['ziduan']);
		$field = json_decode($data['s_field'],true);
		foreach($field['list'] as $k=>$v){
			foreach($v['data'] as $v2){
				if(isset($v2['tpfd_chaxun'])&&($v2['tpfd_chaxun']=='yes')){
					$search[] = $v2;
				}
				if(isset($v2['tpfd_list'])&&($v2['tpfd_list']=='yes')){
					$list[] = $v2;
				}
			}
		}
		$ver = [
			'id'=>$data['id'],
			's_title'=>$field['name'],
			's_db'=>$field['name_db'],
			's_list'=>json_encode($list),
			's_search'=>json_encode($search),
			's_field'=>htmlspecialchars_decode($data['ziduan']),
			's_design'=>1
		];
		db('sfdp_design')->update($ver);;
		return json(['code'=>0]);
	}
	public function sfdp_db($sid){
		$info = db('sfdp_design')->find($sid);
		$field = json_decode($info['s_field'],true);
		foreach($field['list'] as $k=>$v){
			foreach($v['data'] as $v2){
				if(isset($v2['tpfd_db'])){
					$search[] = $v2;
				}
			}
		}
		 $sfdp = new sfdp();
		 $sfdp->makedb($field['name_db'],$search);
	}
	public function sfdp_deldb($sid){
		 $bulid = new BuildTable();
		 $info = db('sfdp_design')->find($sid);
		 $json = json_decode($info['s_field'],true);
		 $ret = $bulid->delDbbak($json['name_db']);
		 if($ret['code']==0){
			 db('sfdp_design')->where('id',$sid)->update(['s_db_bak'=>0]);
		 }
		 return json($ret);
	}
	public function sfdp_fix($sid){
		 $sfdp = new BuildTable();
		$info = db('sfdp_design')->find($sid);
		$json = json_decode($info['s_field'],true);
		$ret = $sfdp->hasDbbak($json['name_db']);
		if($ret['code']==1){
			db('sfdp_design')->where('id',$sid)->update(['s_db_bak'=>1]);
			 return json($ret);
		 }
		$ver = [
			'sid'=>$sid,
			's_bill'=>OrderNumber(),
			's_name'=>$json['name'],
			's_db'=>$json['name_db'],
			's_list'=>$info['s_list'],
			's_search'=>$info['s_search'],
			's_fun_ver'=>'',
			's_field'=>$info['s_field'],
			'add_user'=>1,
			'status'=>1,
			'add_time'=>time()
		];
		$id  =  Db::name('sfdp_design_ver')->insertGetId($ver);
		db('sfdp_design_ver')->where('id','<>',$id)->where('sid',$sid)->update(['status'=>0]);
		$field = json_decode($info['s_field'],true);
		foreach($field['list'] as $k=>$v){
			foreach($v['data'] as $v2){
				if(isset($v2['tpfd_db'])){
					$search[] = $v2;
				}
			}
		}
		 $ret = $sfdp->Btable($json['name_db'],$search);
		 
		 db('sfdp_design')->where('id',$sid)->update(['s_design'=>2,'s_db_bak'=>1]);
		return json(['code'=>0]);
	}
	public function sfdp_create(){
		$sfdp = new sfdp();
		$ver = [
			's_bill'=>OrderNumber(),
			'add_user'=>'Sys',
			's_field'=>1,
			'add_time'=>time()
		];
		Db::name('sfdp_design')->insertGetId($ver);
		echo "<script language='javascript'>alert('Success,创建成功！'); window.location.reload();</script>";
		
	}
	public function sfdp_fun($sid){
		$info = db('sfdp_function')->where('sid',$sid)->find();
		  return view(env('root_path') . 'extend/sfdp/template/sfdp_fun.html',['sid'=>$sid,'info'=>$info]);
	}
	public function sfdp_ui($sid){
		$info = db('sfdp_design')->find($sid);
		if($info['s_design']<>2){
			echo "<script language='javascript'>alert('Err,请先设计并部署！！'); top.location.reload();</script>";
			exit;
		}
		$json = db('sfdp_design_ver')->where('status',1)->where('sid',$sid)->find();
		$field = json_decode($json['s_field'],true);
		foreach($field['list'] as $k=>$v){
			foreach($v['data'] as $v2){
				if(isset($v2['tpfd_db'])){
					$ui[] = $v2;
				}
			}
		}
		return view(env('root_path') . 'extend/sfdp/template/sfdp_ui.html',['sid'=>$sid,'ui'=>$ui]);
	}
	public function sfdp_fun_save(){
		$sfdp = new sfdp();
		$data = input('post.');
		$info = db('sfdp_function')->where('sid',$data['sid'])->find();
		if(!$info){
			$ver = [
				's_bill'=>OrderNumber(),
				'add_user'=>'Sys',
				'sid'=>$data['sid'],
				's_fun'=>$data['function'],
				'add_time'=>time()
			];
			$id = Db::name('sfdp_function')->insertGetId($ver);
			db('sfdp_design_ver')->where('sid',$data['sid'])->where('status',1)->update(['s_fun_id'=>$id,'s_fun_ver'=>$ver['s_bill']]);
			$sfdp->makefun($ver['s_bill'],$data['function']);
			}else{
			$ver = [
				'id'=>$info['id'],
				's_fun'=>$data['function']
			];	
			Db::name('sfdp_function')->update($ver);
			$sfdp->makefun($info['s_bill'],$data['function']);
			echo "<script language='javascript'>alert('Success,脚本生成成功！'); top.location.reload();</script>";
		}
		
	}
}
