<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 数据类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\adaptive;


use sfdp\fun\SfdpUnit;
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
	static function getListData($sid,$map,$page=1,$limit=10){
		$jsondata = Design::descVerTodata($sid);
		$list = (new Data())->mode->select($jsondata['db_name'],$map,$jsondata['field'],$page,$limit);
		$json = $list['data'];
		foreach ($json as $k => $v) {
			foreach($jsondata['fieldArr'] as $k2=>$v2){
				$json[$k][$k2] = $jsondata['fieldArr'][$k2][$v[$k2]] ?? '<font color="red">索引出错</font>';
			}
		}
		return ['count'=>$list['count'],'list'=>$json,'field'=>$jsondata,'title'=>$jsondata['title']];
	}
	static function getEditData($sid,$bid){
		$data = Design::getAddData($sid);
		$sfdp_ver_info = Design::findVer($sid);
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$find = (new Data())->mode->find($field['name_db'],$bid);
		foreach($field['list'] as $k=>$v){
				foreach($v['data'] as $k2=>$v2){
					if(isset($v2['xx_type']) && $v2['xx_type']==1){
						//函数名转为数据信息
						$map[] = ['fun_name','=',$v2['checkboxes_func']];
						$getFun = Functions::findWhere($map);
						if(!$getFun){
							echo '<h2>系统级别错误('.$v2['checkboxes_func'].')：函数名无法找到~</h2>';exit;
						}
						$getData = Common::query($getFun['function']);
						if($getData['code']==-1){
							echo '<h2>系统级错误：'.$getData['msg'].'</h2>';exit;
						}else{
							$tpfd_data = [];
							foreach($getData['msg'] as $k3=>$v3){
								$tpfd_data[$v3['id']] = $v3['name'];
							}
						}
						$v2['tpfd_data'] = $tpfd_data;
					}else{
						if(isset($v2['tpfd_data'])){
							$tpfd_data = $v2['tpfd_data'];
						}
					}
					if($v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes'){
						$value_arr = explode(",",$find[$v2['tpfd_db']]);
						$fiedsver = '';
						foreach($value_arr as $v3){
							$fiedsver .=$v2['tpfd_data'][$v3].',';
						}
						$field['list'][$k]['data'][$k2]['tpfd_data'] = $tpfd_data;
						$field['list'][$k]['data'][$k2]['value'] = rtrim($fiedsver, ',');
					}else{
						$field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
					}
					
				}
		}
		return ['info'=>json_encode($field),'data'=>$data];
	}
	static function getViewData($sid,$bid){
		$sfdp_ver_info = Design::findVer($sid);
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$find = (new Data())->mode->find($field['name_db'],$bid);
		foreach($field['list'] as $k=>$v){
				foreach($v['data'] as $k2=>$v2){
					if(isset($v2['xx_type']) && $v2['xx_type']==1){
						//函数名转为数据信息
						$map[] = ['fun_name','=',$v2['checkboxes_func']];
						$getFun = Functions::findWhere($map);
						if(!$getFun){
							echo '<h2>系统级别错误('.$v2['checkboxes_func'].')：函数名无法找到~</h2>';exit;
						}
						$getData = Common::query($getFun['function']);
						if($getData['code']==-1){
							echo '<h2>系统级错误：'.$getData['msg'].'</h2>';exit;
						}else{
							$tpfd_data = [];
							foreach($getData['msg'] as $k3=>$v3){
								$tpfd_data[$v3['id']] = $v3['name'];
							}
						}
						$v2['tpfd_data'] = $tpfd_data;
					}else{
						if(isset($v2['tpfd_data'])){
							$tpfd_data = $v2['tpfd_data'];
						}
					}
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