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
namespace sfdp\adaptive;

use think\Db;

use sfdp\fun\BuildFun;
use sfdp\fun\SfdpUnit;
use sfdp\fun\BuildTable;

use sfdp\lib\unit;


class Design{

	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('db_mode')==1){
			$className = '\\sfdp\\custom\\AdapteeDesign';
		}else{
			$className = unit::gconfig('db_namespace').'AdapteeDesign';
		}
		$this->mode = new $className();
    }
	/**
     * find 获取设计信息  getDesign
     * @param array $whereRaw raw查询条件
	 * @param array $map 查询条件
     */
    static function find($sid)
    {
		return (new Design())->mode->find($sid);
    }
	/**
     * getDesignJson 获取设计信息  getDesign
     * @param array $whereRaw raw查询条件
	 * @param array $map 查询条件
     */

	static function getDesignJson($sid){
		$info = (new Design())->mode->find($sid);
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
	static function getDesignVer($status=1)
    {
		return (new Design())->mode->getDesignVer($status);
	}
	/**
     * 获取设计版本 getDescVerVal
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	static function findVer($id)
    {
		return (new Design())->mode->findVer($id);
	}
	/**
     * 获取设计版本  descVerTodata
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	static function descVerTodata($sid){
		$sfdp_ver_info =(new Design())->mode->findVer($sid);
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$list_field = json_decode($sfdp_ver_info['s_list'],true);
		$searct_field = $sfdp_ver_info['s_search'];
		$listid = ''; //变量赋值为空
		$listfield = []; //变量赋值为空
			foreach($list_field as $key=>$vals){
				$listid.=$vals['tpfd_db'].',';
				$listfield[$vals['tpfd_db']]=$vals['tpfd_name'];
			}
		$fieldArr = [];
		$fieldArrAll = [];
			foreach($field['list'] as $k=>$v){
				foreach($v['data'] as $k2=>$v2){
					//xx_type //tpfd_data //td_type
					if(($v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes')and($v2['tpfd_list']=='yes')){
						$fieldArr[$v2['tpfd_db']]=$v2['tpfd_data'];
					}
					if($v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes'){
						$fieldArrAll[$v2['tpfd_db']]=$v2['tpfd_data'];
					}
				}
			}
		$load_file = SfdpUnit::Loadfile($field['name_db'],$field['tpfd_class'],$field['tpfd_script']);
		return ['sid'=>$sfdp_ver_info['id'],'db_name'=>$field['name_db'],'load_file'=>$load_file,'btn'=>$field['tpfd_btn'],'field'=>rtrim($listid, ','),'fieldname'=>$listfield,'search'=>$searct_field,'title'=>$sfdp_ver_info['s_name'],'fieldArr'=>$fieldArr,'fieldArrAll'=>$fieldArrAll];
	}
	static function getAddData($sid){
		$sfdp_ver_info = (new Design())->mode->findVer($sid);
		if($sfdp_ver_info['s_fun_id']!=''){
			$fun = '<script src="/static/sfdp/user-defined/'.$sfdp_ver_info['s_fun_ver'].'.js"></script>';	
		}else{
			$fun = '';
		}
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$load_file = SfdpUnit::Loadfile($field['name_db'],$field['tpfd_class'],$field['tpfd_script']);
		return ['info'=>$sfdp_ver_info,'fun'=>$fun,'load_file'=>$load_file];
	}
	static function getViewData($sid,$bid){
		$sfdp_ver_info = (new Design())->mode->findVer($sid);
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$find = Db::name($field['name_db'])->find($bid);
		foreach($field['list'] as $k=>$v){
				foreach($v['data'] as $k2=>$v2){
					if($v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes'){
						$value_arr = explode(",",$find[$v2['tpfd_db']]);
						$fiedsver = '';
						foreach($value_arr as $v3){
							$fiedsver .=$v2['tpfd_data'][$v3].',';
						}
						$field['list'][$k]['data'][$k2]['value'] = rtrim($fiedsver, ',');
					}else{
						$field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
					}
				}
		}
		return ['info'=>json_encode($field)];
	}
	/**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
	static function getListData($sid,$map){
		$jsondata = self::descVerTodata($sid);
		$list = Db::name($jsondata['db_name'])->where($map)->field('id,'.$jsondata['field'])->order('id desc')->paginate(10);
		$list = $list->all();
		foreach ($list as $k => $v) {
			foreach($jsondata['fieldArr'] as $k2=>$v2){
				$list[$k][$k2] = $jsondata['fieldArr'][$k2][$v[$k2]] ?? '<font color="red">索引出错</font>';
			}
		}
		$jsondata['search'] = SfdpUnit::mergesearch($map,$jsondata['search']);
		return ['list'=>$list,'field'=>$jsondata,'title'=>$jsondata['title']];
	}
	static function saveDesc($data,$type='save'){
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
			if(empty($list)){
				return ['code'=>1,'msg'=>'Sorry,未能找到列表参数'];
			}
		
			$isTable=Db::query('SHOW TABLES LIKE '."'wf_".$field['name_db']."'");
			if($isTable){
				//return ['code'=>1,'msg'=>'数据表名已经存在~'];
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
			if(Db::name('sfdp_design')->update($ver)){
				return ['code'=>0,'msg'=>'Success'];
			}else{
				return ['code'=>1,'msg'=>'Sorry,更新失败~'];
			}
			
		}elseif($type=='update'){
			return Db::name('sfdp_design')->update($data);;
		}else{
			$ver = [
				's_bill'=>unit::OrderNumber(),
				'add_user'=>'Sys',
				's_field'=>1,
				'add_time'=>time()
			];
			return Db::name('sfdp_design')->insertGetId($ver);
		}
	}
}