<?php
/**
 *+------------------
 * Tpfd 表单控件
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace sfdp;

use think\Db;
use think\facade\Session;

class FbDb{
	
	/**
	 * 表单设计
	 **/
	public static function Fbdesc($fid)
	{
	   $fun = Db::name('fb_fun')->where('fid',$fid)->select();
	   $html = '';
	   if(count($fun)>=1){
		   foreach($fun as $k=>$v){
			    $data = Db::query($v['sql']);
				
				 $options = '';
				 foreach($data as $k2=>$v2){
					 $options .= '{"label":"'.$v2["name"].'","checked":false,"value":"'.$v2["id"].'"},';
				 }
			    $html .= '{"label":"'.$v["name"].'","field_type":"dropdown","required":true,"field_options":{"options":['.rtrim($options, ",").'],"include_blank_option":false,"size":"medium"},"lists":"yes","search":"no","type":"select","fun":"yes","cid":"c10","name":"'.$v["zdname"].'"},';
		   }
	   }
	   $zd = '['.rtrim($html, ",").']';
	   return ['field'=>$zd,'fid'=>$fid];
	}
	public static function Fbedit($id)
	{
	   $info = Db::name('fb')->find($id);
	   $field = json_decode($info['ziduan'],true);
	   $info['field'] =  json_encode($field['fields']);
	   return ['info'=>$info,'id'=>$id];
	}
	public static function Fbsave($data)
	{
	   $ret = Db::name('fb')->where('id', $data['id'])->update($data);
	   if(!$ret){
		   return ['status' => 0, 'msg' => '操作失败', 'info' => ''];
	   }
	    return ['status' => 1, 'msg' => '添加成功！', 'info' => ''];
	}
	public static function FBview($id,$type)
	{
		$info = Db::name('fb')->find($id);
		$ziduan = json_decode($info['ziduan'],true);
		$field = [];
		$form = [];
		if(count($ziduan['fields'])==0){
			 echo '您还没设计呀~';
			 exit;
		}
		if($type=='Act'){
			$type = $info['name'];
		}
		foreach($ziduan['fields'] as $k=>$v){
			$field[$k]['name'] = $v['name'];
			$field[$k]['type'] = 'text';
			$field[$k]['extra'] = '';
			$field[$k]['comment'] = $v['label'];
			$field[$k]['default'] = '';
			if($v['fun']=='yes'){
				$form[$k]['fun_con'] = Db::name('fb_fun')->where('fid',$id)->where('zdname',$v['name'])->find();
			}
			$form[$k]['fun'] = $v['fun'] ?? 'no';
			$form[$k]['title'] =  $v['label'];
			$form[$k]['name'] =  $v['name'];
			$form[$k]['type'] =  $v['field_type'];
			$form[$k]['option'] =  $v['field_options'];
			$form[$k]['default'] = '';
			$form[$k]['search'] = $v['search'];
			$form[$k]['lists'] = $v['lists'];
		}
		$data = [
			'file'=>$info['file'],
			'module'=>'index',
			'controller'=>$type,
			'menu'=>['add,del'],
			'title'=>$info['title'],
			'flow'=>$info['flow'],
			'table'=>$info['name'],
			'field'=>$field,
			'form'=>$form
		];
	    return $data;
	}
	
	
	
	
}