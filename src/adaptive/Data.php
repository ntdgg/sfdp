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
	/**
	 * 添加
	 */
	static function add($sid,$data){
		foreach($data as $k=>$v){
			if(is_array($v)){
				$data[$k] = implode(",", $v);
			}
		}
		$data['uid']=unit::getuserinfo('uid');
		$data['create_time']=time();
		$table = $data['name_db'];
		unset($data['name_db']);
		unset($data['tpfd_check']);
		$subdata  = json_decode($data['@subdata'],true);
		unset($data['@subdata']);
		$did = (new Data())->mode->add($table,$data);
		if(!$did){
			return $did;
		}
		if(is_array($subdata) && count($subdata)>0 ){
			$subDatas = [];
			foreach($subdata as $k=>$v){
				$keys = array_keys($v);//取出所有
				$num = count($v[$keys[0]]);//计算下有几列数据
				//数组转换，将值合并
				for ($x=0; $x<$num; $x++) {
					$new[$x] = [];
				    foreach($keys as $key){
						//相同顺序的值Push到同个数组
						array_push($new[$x],[$key=>$v[$key][$x]]);
					}
				}
				//将值对应转成二维数组
				foreach($new as $kk=>$vv){
					//二维数组转一维数组，并赋值给对应的数据表
					$subDatas[$k][] = array_reduce($vv, 'array_merge', array());
				}
			}
			foreach($subDatas as $k=>$v){
				$subTable = $k;
				foreach($v as $kk=>$vv){
					$vv['uid']=unit::getuserinfo('uid');
					$vv['create_time']=time();
					$vv['d_id']=$did;
					$ret = (new Data())->mode->add($subTable,$vv);
					if(!$ret){
						return $ret;
					}
				}
			}
		}
		return 	true;
	}
	/**
	 * 删除
	 */
	static function del($sid,$id){
		$sfdp_ver_info = Design::findVer($sid);
		$field = json_decode($sfdp_ver_info['s_field'],true);
		$sublist =[];
		if($field['sublist']!='' && is_array($field['sublist']) && count($field['sublist'])>0){
			$Stable = self::getSubData($field['name_db'],$field['sublist']);
			foreach($Stable as $k=>$v){	
				$ret = self::delSub($v[0],$id);
				if(!$ret){
					return $ret;
				}
			}
		}
		
		$table = $sfdp_ver_info['s_db'];
		return (new Data())->mode->del($table,$id);		
	}
	/**
	 * 删除
	 */
	static function delSub($table,$id){
		return (new Data())->mode->delSub($table,$id);		
	}
	/**
	 * 编辑修改
	 */
	static function edit($sid,$data,$id){
		foreach($data as $k=>$v){
			if(is_array($v)){
				$data[$k] = implode(",", $v);
			}
		}
		$table = $data['name_db'];
		$data['update_time'] = time();
		unset($data['name_db']);
		unset($data['tpfd_check']);
		$subdata  = json_decode($data['@subdata'],true);
		unset($data['@subdata']);
		//修改主表数据
		$ret = (new Data())->mode->edit($table,$data,$id);
		if(!$ret){
			return $ret;
		}
		if(is_array($subdata) && count($subdata)>0 ){
			$subDatas = [];
			foreach($subdata as $k=>$v){
				$keys = array_keys($v);//取出所有
				$num = count($v[$keys[0]]);//计算下有几列数据
				//数组转换，将值合并
				for ($x=0; $x<$num; $x++) {
					$new[$x] = [];
				    foreach($keys as $key){
						//相同顺序的值Push到同个数组
						array_push($new[$x],[$key=>$v[$key][$x]]);
					}
				}
				//将值对应转成二维数组
				foreach($new as $kk=>$vv){
					//二维数组转一维数组，并赋值给对应的数据表
					$subDatas[$k][] = array_reduce($vv, 'array_merge', array());
				}
			}
			foreach($subDatas as $k=>$v){
				$subTable = $k;
				self::delSub($subTable,$id);
				foreach($v as $kk=>$vv){
					$vv['uid']=unit::getuserinfo('uid');
					$vv['create_time']=time();
					$vv['d_id']=$id;
					$ret = (new Data())->mode->add($subTable,$vv);
					if(!$ret){
						return $ret;
					}
				}
			}
		}
		return 	true;	
	}
	/**
	 * 获取列表数据
	 */
	static function getListData($sid,$map,$page=1,$limit=10){
		$jsondata = Design::descVerTodata($sid);
		$Modue = Modue::find($jsondata['sid']);
		/*权限系统组合过滤*/
		$access = json_decode($Modue['access'],true);
		foreach((array)$access as $kk=>$vv){
			$field = Field::find($vv[0]);
			$value =unit::getuserinfo($vv[2]);
			if($vv[1]=='in'){
				$eq ='find in set';
			}else if($vv[1]=='no in'){
				$eq ='NOT REGEXP';
				$value ='(^|,)('.$value.')(,|$)';
			}else{
				$eq =$vv[1];
			}
			$map[] = [$field['field'],$eq,$value];
		}
		
		/*权限系统组合过滤*/
		$list = (new Data())->mode->select($jsondata['db_name'],$map,$Modue['field'],$page,$limit);
		$json = $list['data'];
		foreach ($json as $k => $v) {
			foreach($jsondata['fieldArrAll'] as $k2=>$v2){
				if(in_array($k2,explode(',',$Modue['field']))){
					if(strpos($v[$k2],',') !== false){
						$vk2 = explode(',',$v[$k2]);
						$vk2value ='';
						foreach($vk2 as $vvv){
							$vk2value .=$jsondata['fieldArrAll'][$k2][$vvv].',';
						}
						$json[$k][$k2] = rtrim($vk2value, ",");
						
					}else{
						$json[$k][$k2] = $jsondata['fieldArrAll'][$k2][$v[$k2]] ?? '<font color="red">索引出错</font>';
					}
				}
			}
		}
		
		return ['count'=>$list['count'],'list'=>$json,'field'=>$jsondata,'title'=>$jsondata['title']];
	}
	static function selectAll($table,$where=[]){
		return  (new Data())->mode->selectAll($table,$where);
	}
	/**
	 * 修改编辑
	 */
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
		//字表数据
		$sublist =[];
		if(isset($field['sublist']) && $field['sublist']!='' && is_array($field['sublist']) && count($field['sublist'])>0){
			$Stable = self::getSubData($field['name_db'],$field['sublist']);
			foreach($Stable as $k=>$v){	
				
				$sublist[$k]= self::selectAll($v[0],['d_id'=>$bid]);
				$datas = self::selectAll($v[0],['d_id'=>$bid]);
				
				foreach($datas as $kk=>$vv){
					$mkey = array_keys($v[1]);
					$mkey2 = array_keys($vv);
					foreach($mkey as $kkk=>$vvv){
						$v[1][$vvv]['value'] =$vv[$mkey2[$kkk]];
					}
					$sublist[$k][$kk] = $v[1];
				}
			}
		}
		$field['sublists'] = $sublist;
		return ['info'=>json_encode($field),'sublist'=>json_encode($sublist),'data'=>$data];
	}
	static function getSubData($table,$data){
		$buile_table =[];
		$i = 1;
		foreach($data as $k=>$v){
			if($v['data']!='' && is_array($v['data']) && count($v['data'])>0){
				$buile_table[$i-1][] = $table.'_d'.$i;
				$buile_table[$i-1][] = $v['data'];
				$i++;
			}
		}
		return $buile_table;
	}
	/**
	 * 获取数据方法
	 */
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
		//字表数据
		$sublist =[];
		if(isset($field['sublist']) && $field['sublist']!='' && is_array($field['sublist']) && count($field['sublist'])>0){
			$Stable = self::getSubData($field['name_db'],$field['sublist']);
			foreach($Stable as $k=>$v){	
				
				$sublist[$k]= self::selectAll($v[0],['d_id'=>$bid]);
				$datas = self::selectAll($v[0],['d_id'=>$bid]);
				
				foreach($datas as $kk=>$vv){
					$mkey = array_keys($v[1]);
					$mkey2 = array_keys($vv);
					foreach($mkey as $kkk=>$vvv){
						$v[1][$vvv]['value'] =$vv[$mkey2[$kkk]];
					}
					$sublist[$k][$kk] = $v[1];
				}
			}
		}
		$field['sublists'] = $sublist;
		return ['info'=>json_encode($field)];
	}

	
	
	
	
}