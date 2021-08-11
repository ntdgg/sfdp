<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V5.0
 *+------------------
 * Sfdp 构建函数方法
 *+------------------
 * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace sfdp\fun;

use sfdp\adaptive\Common;
use sfdp\adaptive\Design;
use sfdp\adaptive\View;
use sfdp\fun\BuildTable;
use sfdp\lib\unit;
use think\facade\Db;

class BuildFix{
	/**
	 * 创建数据表
	 */
	public static function Bfix($sid)
	{
		$info = Design::find($sid);
		$json = Design::getDesignJson($sid);
		if(isset($json['sublist']) && $json['sublist']!='' && is_array($json['sublist']) && count($json['sublist'])>0){
			return ['code'=>-1,'msg'=>'对不起，暂时不支持子表调整，请使用部署功能！'];
		}
		$ret =  BuildTable::hasDbbak($json['name_db']);
		if($ret['code']==1){
			Design::saveDesc(['s_db_bak'=>1,'s_look'=>1,'id'=>$sid],'update');
			return $ret;
		}
		
		$check_old = Db::name('sfdp_design_ver')->where('sid',$info['id'])->where('status',1)->find();
		$try_fields = Db::name('sfdp_field')->where('sid',$check_old['id'])->where('field','not in',['id','uid','status','create_time','update_time'])->where('fid','')->select();
		//>0说明没有关联 fid
		if(count($try_fields)>0){
			$all = json_decode($check_old['s_field'],true);
			foreach($all['list'] as $k=>$v) {
				foreach($v['data'] as $k2=>$v2) {
					Db::name('sfdp_field')->where('sid',$check_old['id'])->where('field',$v2['tpfd_db'])->where('fid','')->update(['fid'=>$v2['tpfd_id']]);//更新fid回去
				}
			}
		}
		$old_verid = $check_old['id'];
		/*新的设计写入版本库*/
		$varInfo = View::verAdd($sid,$info,$json);
		$all = json_decode($varInfo['all'],true);
		$sql =[];
		$update =[];
		$addfield =[];
		$delfield =[];
		$table = unit::gconfig('int_db_prefix').$all['name_db'];
		$varId = $varInfo['ver']['id'];
		/*调整和获取版本库的列表字段信息提供修改！*/
		$old_modue = Db::name('sfdp_modue')->where('sid',$old_verid)->find();
		if(!$old_modue){
			return ['code'=>-1,'msg'=>'对不起，更新出错！'];
		}
		$old_modue_field = explode(',',$old_modue['field']);
		$new_modue_field = explode(',',$old_modue['field']);
		$old_modue_name = explode(',',$old_modue['field_name']);
		$new_modue_name = explode(',',$old_modue['field_name']);
		/*比对新的字段信息*/
		foreach($varInfo['db'] as $k=>$v){
			$allfield[] = $v['tpfd_id'];
			$fiels = Db::name('sfdp_field')->where('sid',$old_verid)->where('fid',$v['tpfd_id'])->find();
			//更新字段信息
			if($fiels){
				//更新字段名
				if($v['tpfd_db']!=$fiels['field']){
					if($v['tpfd_dblx']=='datetime'||$v['tpfd_dblx']=='longtext'||$v['tpfd_dblx']=='date'){
						$sql[] = "alter table {$table} CHANGE  {$fiels['field']} {$v['tpfd_db']} {$fiels['name_type']};";
					}else{
						$sql[] = "alter table {$table} CHANGE  {$fiels['field']} {$v['tpfd_db']} {$fiels['name_type']}({$fiels['length']});";
					}
					//更新字段列表信息
					if(in_array($fiels['field'],$old_modue_field)){
						$new_modue_field[array_search($fiels['field'],$old_modue_field)] =$v['tpfd_db'];
					}
				}
				//更新字段类型及字段名
				if($v['tpfd_dblx']!=$fiels['name_type'] || $v['tpfd_dbcd']!=$fiels['length']){
					if($v['tpfd_dblx']=='datetime'||$v['tpfd_dblx']=='longtext'||$v['tpfd_dblx']=='date'){
						$sql[] = "alter table {$table}  modify column {$v['tpfd_db']} {$v['tpfd_dblx']};";
					}else{
						$sql[] = "alter table {$table}  modify column {$v['tpfd_db']} {$v['tpfd_dblx']}({$v['tpfd_dbcd']});";
					}
				}
				//更新字段注释
				if($v['tpfd_name']!=$fiels['name']){
					if($v['tpfd_dblx']=='datetime'||$v['tpfd_dblx']=='longtext'||$v['tpfd_dblx']=='date'){
						$sql[] = "ALTER TABLE {$table} MODIFY COLUMN {$v['tpfd_db']} {$v['tpfd_dblx']} COMMENT '{$v['tpfd_name']}';";
					}else{
						$sql[] = "ALTER TABLE {$table} MODIFY COLUMN {$v['tpfd_db']} {$v['tpfd_dblx']}({$v['tpfd_dbcd']}) COMMENT '{$v['tpfd_name']}';";
					}
					//更新字段列表名称
					if(in_array($fiels['name'],$old_modue_name)){
						$new_modue_name[array_search($fiels['name'],$old_modue_name)] =$v['tpfd_name'];
					}
				}
				$update[$fiels['id']] =['field'=>$v['tpfd_db'],'name_type'=>$v['tpfd_dblx'],'length'=>$v['tpfd_dbcd'],'name'=>$v['tpfd_name']];
			}else{
				//新增字段信息
				if($v['tpfd_dblx']=='datetime'||$v['tpfd_dblx']=='longtext'||$v['tpfd_dblx']=='date'){
					$sql[] = "ALTER TABLE {$table} ADD COLUMN {$v['tpfd_db']} {$v['tpfd_dblx']} NULL COMMENT '{$v['tpfd_name']}';";
				}else{
					$sql[] = "ALTER TABLE {$table} ADD COLUMN {$v['tpfd_db']} {$v['tpfd_dblx']}({$v['tpfd_dbcd']})  NULL COMMENT '{$v['tpfd_name']}';";
				}
				$addfield[] = [
					'sid'=>$varId,
					'field'=>$v['tpfd_db'],
					'name'=>$v['tpfd_name'],//字段名
					'name_type'=>$v['tpfd_dblx'],
					'zanwei'=>$v['tpfd_zanwei'] ?? '',//占位标识
					'moren'=>$v['tpfd_moren'] ?? '',//默认标识
					'is_request'=>$v['tpfd_must'] ?? '',
					'is_read'=>$v['tpfd_read'] ?? '',
					'length'=>$v['tpfd_dbcd'],
					'type_data'=>json_encode($v['tpfd_data'] ?? '',true),
					'type'=>$v['td_type'],//输入类型
					'data'=>$v['xx_type'] ?? '',
					'function'=>$v['checkboxes_func'] ??'',
					'type_lx'=>1,
					'is_list'=>$v['tpfd_list']=='yes'?1:0,
					'is_search'=>$v['tpfd_chaxun']=='yes'?1:0,
					'fid'=>$v['tpfd_id'],
					'search_type'=>''
				];
			}
		}
		
		//删除的字段信息
		$delfiels = Db::name('sfdp_field')
			->where('sid',$old_verid)
			->where('field','not in',['id','uid','status','create_time','update_time'])
			->where('fid','not in',$allfield)->select()->all();
		foreach($delfiels as $k=>$v){
			$sql[] = "ALTER TABLE {$table} drop COLUMN {$v['field']};";
			$delfield[] = $v['id'];
		}
		try{
			//如果SQL 大于0说明需要执行SQL语句
			if(count($sql)>0){
				/*如果数据表有变更，创建一个备份表*/
				Db::execute("create table {$table}_bak like {$table};");
				Db::execute("INSERT INTO {$table}_bak SELECT * FROM {$table};");
				foreach($sql as $v){
					Db::execute($v);
				}
			}
		}catch(\Exception $e){
			//Db::name('sfdp_design_ver')->delete($varId);//更新出错，恢复数据下
			return ['code'=>-1,'msg'=>'SQL_Err:'.$e->getMessage()];
		}
		//启动事务处理
		Db::startTrans();
		try {
			Db::name('sfdp_field')->where('sid',$old_verid)->update(['update_time'=>time(),'sid'=>$varId]);
			Db::name('sfdp_modue')->where('sid',$old_verid)
				->update(['sid'=>$varId,'update_time'=>time(),'btn'=>implode(',',$json['tpfd_btn']),'field_name'=>implode(',',$new_modue_name),'field'=>implode(',',$new_modue_field)]);
			/*插入新的字段信息*/
			if(count($addfield)>0){
				Db::name('sfdp_field')->insertAll($addfield);
			}
			/*更新字段的信息*/
			if(count($update)>0){
				foreach($update as $k=>$v){
					Db::name('sfdp_field')->where('id',$k)->update($v);
				}
			}
			/*删除字段*/
			if(count($delfield)>0){
				foreach($delfield as $v){
					Db::name('sfdp_field')->delete($v);
				}
			}
			//更新设计表
			Design::saveDesc(['s_db_bak'=>1,'s_look'=>1,'s_design'=>2,'id'=>$sid],'update');
			Db::commit();
			return ['code'=>0,'msg'=>'更新主表信息成功！！'];
		}catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			return ['code'=>-1,'msg'=>'执行出错：'.$e->getMessage()];
		}
	}
}