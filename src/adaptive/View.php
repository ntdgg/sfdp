<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 视图类
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
  *+------------------
  */
namespace sfdp\adaptive;

use sfdp\lib\unit;

class View{
	
	/**
	 * 版本号
	 */
	public static function ver($sid){
		$json = Design::findVerWhere([['status','=',1],['sid','=',$sid]]);
		$field = json_decode($json['s_field'],true);
		$data_ver_db = [];
        $data_ver_db_id = [];
		foreach((array)$field['list'] as $k=>$v){
			foreach($v['data'] as $v2){
				if(isset($v2['tpfd_db'])){
					$data_ver_db[] = $v2;
                    $data_ver_db_id[$v2['tpfd_id']] = $v2;
				}
			}
		}
        $data_ver_db2 = [];
        foreach((array)$field['sublist'] as $k=>$v){
                foreach ($v['data'] as $v3) {
                    if (isset($v3['tpfd_db'])) {
                        $data_ver_db2[$k][] = $v3;
                    }
            }
        }
		return ['db'=>$data_ver_db,'db_id'=>$data_ver_db_id,'db2'=>$data_ver_db2,'all'=>$json['s_field'],'ver'=>$json];
	}
	/**
	 * 保存版本
	 */
	public static function SaveVer($sid,$data){
		$json = Design::findVerWhere([['status','=',1],['sid','=',$sid]]);
		$field = json_decode($json['s_field'],true);
		$new_list =[];
		foreach($field['list'] as $k=>$v){
			foreach($v['data'] as $k2=>$v2){
				if(in_array($v2['tpfd_id'],$data)){
					$field['list'][$k]['data'][$k2]['tpfd_list'] ='yes';
					$new_list[] = $v2;
				}else{
					$field['list'][$k]['data'][$k2]['tpfd_list'] ='no';
				}
			}
		}
		$s_field = json_encode($field);//排序后的新列表字段数据
		$s_list =json_encode($new_list);
		return Design::updateVer(['id'=>$json['id'],'s_field'=>$s_field,'s_list'=>$s_list]);
	}
	/**
	 * 添加版本
	 */
	public static function verAdd($sid,$info,$json){
		$json_ver = Design::findVerWhere([['status','=',1],['sid','=',$sid]]);//2021年5月12日 重新部署更新脚本方法
		$ver = [
			'sid'=>$sid,
			's_bill'=>unit::OrderNumber(),
			's_name'=>$json['name'],
			's_db'=>$json['name_db'],
			's_list'=>$info['s_list'],
			's_search'=>$info['s_search'],
			's_fun_ver'=>$json_ver['s_fun_ver']  ?? '',
			's_fun_id'=>$json_ver['s_fun_id']  ?? '',
			's_field'=>$info['s_field'],
			'add_user'=>1,
			'status'=>1,
			'add_time'=>time()
		];
		$id  = Design::insertVer($ver);
		//将历史版本库禁用
		Design::updateVerWhere([['id','<>',$id],['sid','=',$sid]],['status'=>0]);
		return self::ver($sid);
		
	}
}