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

use think\Db;
use think\facade\Session;

class DescDb{
	
	public static function getDesign($sid){
		$info = Db::name('sfdp_design')->find($sid);
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}
	public static function getDesignJson($sid){
		$info = Db::name('sfdp_design')->find($sid);
		$json = json_decode($info['s_field'],true);
		if($info){
			return $json;
		}else{
			return  false;
		}
	}
	
	 /**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	 public static function getDescVer($status=1)
    {
		 $info = Db::name('sfdp_design_ver')->where('status',$status)->select();
        return $info;
		
	}
	/**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	 public static function getDescVerVal($id)
    {
		 $info = Db::name('sfdp_design_ver')->find($id);
        return $info;
		
	}
	/**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	public static function descVerTodata($sid){
		$sfdp_ver_info = self::getDescVerVal($sid);
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$list_field = json_decode($sfdp_ver_info['s_list'],true);
		$topicid = ''; //变量赋值为空
		$topicname = []; //变量赋值为空
			//用foreach 遍历下二维数组
			foreach($list_field as $key=>$vals){
				$topicid.=$vals['tpfd_db'].',';
				$topicname[$vals['tpfd_db']]=$vals['tpfd_db'];
			}
		$topicid = rtrim($topicid, ',');
		return ['db_name'=>$field['name_db'],'field'=>$topicid,'fieldname'=>$topicname];
	}
	/**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	public static function getListData($sid){
		$jsondata = self::descVerTodata($sid);
		$list = Db::name($jsondata['db_name'])->field($jsondata['field'])->paginate('10');
		return ['list'=>$list,'field'=>$jsondata];
	}
	public static function saveDesc($data,$type='save'){
		if($type=='save'){
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
			return Db::name('sfdp_design')->update($ver);;
			
		}elseif($type=='update'){
			return Db::name('sfdp_design')->update($data);;
		}else{
			$ver = [
				's_bill'=>OrderNumber(),
				'add_user'=>'Sys',
				's_field'=>1,
				'add_time'=>time()
			];
			return Db::name('sfdp_design')->insertGetId($ver);
		}
		
		
	}
}