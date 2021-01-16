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

class Data{
	
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('db_mode')==1){
			$className = '\\sfdp\\custom\\AdapteeData';
		}else{
			$className = unit::gconfig('db_namespace').'AdapteeData';
		}
		$this->mode = new $className();
    }
	static function add($sid,$data){
		foreach($data as $k=>$v){
			if(is_array($v)){
				$data[$k] = implode(",", $v);
			}
		}
		$table = $data['name_db'];
		unset($data['name_db']);
		unset($data['tpfd_check']);
		return (new Data())->mode->add($table,$data);		
	}
	static function getListData($sid,$map){
		$jsondata = Design::descVerTodata($sid);
		$list = (new Data())->mode->select($jsondata['db_name'],$map,$jsondata['field']);
		$list = $list->all();
		foreach ($list as $k => $v) {
			foreach($jsondata['fieldArr'] as $k2=>$v2){
				$list[$k][$k2] = $jsondata['fieldArr'][$k2][$v[$k2]] ?? '<font color="red">索引出错</font>';
			}
		}
		$jsondata['search'] = SfdpUnit::mergesearch($map,$jsondata['search']);
		return ['list'=>$list,'field'=>$jsondata,'title'=>$jsondata['title']];
	}
	static function getViewData($sid,$bid){
		$sfdp_ver_info = Design::findVer($sid);
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$find = (new Data())->mode->find($field['name_db'],$bid);
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

	
	
	
	
}