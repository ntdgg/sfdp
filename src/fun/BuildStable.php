<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 构建数据库表
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */

namespace sfdp\fun;

use think\Exception;
use sfdp\lib\unit;
use sfdp\adaptive\Common;


class BuildStable{
	
	/**
     * 创建数据表
     */
    static function Btable($table,$data)
    {
		$fieldAttr = [];
		$key = [];
		$i = 1;
		$auto_create_field = ['id','uid','status', 'create_time', 'update_time'];
		foreach($data as $k=>$v){
			if($v['data']!='' && is_array($v['data']) && count($v['data'])>0){
				$buile_table = unit::gconfig('int_db_prefix').$table.'_d'.$i;
				$tableExist = false;
				// 判断表是否存在,如果存在，就创建个备份数据表
				$ret = Common::query("SHOW TABLES LIKE '{$buile_table}'");
				if ($ret && isset($ret['msg'][0])) {
					Common::execute("RENAME TABLE {$buile_table} to {$buile_table}_bak");
					$tableExist = true;
				}
				$fieldAttr[] = unit::tab(1) . "`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'";
				$fieldAttr[] = unit::tab(1) . "`d_id` int(10) DEFAULT '0' COMMENT '关联主表id'";
				foreach ($v['data'] as $v) {
						if (!in_array($v['tpfd_db'], $auto_create_field)) {
							if($v['tpfd_dblx']=='datetime'||$v['tpfd_dblx']=='longtext'){
								$fieldAttr[] = unit::tab(1) . "`{$v['tpfd_db']}` {$v['tpfd_dblx']} COMMENT '{$v['tpfd_name']}'";
								}else{
								$fieldAttr[] = unit::tab(1) . "`{$v['tpfd_db']}` {$v['tpfd_dblx']}({$v['tpfd_dbcd']}) DEFAULT NULL COMMENT '{$v['tpfd_name']}'";
							}
						}
					}
				$fieldAttr[] = unit::tab(1) . "`uid` int(10) DEFAULT '0' COMMENT '用户id'";
				$fieldAttr[] = unit::tab(1) . "`status` int(10)  DEFAULT '0' COMMENT '审核状态'";
				$fieldAttr[] = unit::tab(1) . "`create_time` int(10)  DEFAULT '0' COMMENT '新增时间'";
				$fieldAttr[] = unit::tab(1) . "`update_time` int(10)  DEFAULT '0' COMMENT '更新时间'";
				$fieldAttr[] = unit::tab(1) . "PRIMARY KEY (`id`)";
				$sql_drop = "DROP TABLE IF EXISTS `{$buile_table}`";//删除数据表
				$sql_create = "CREATE TABLE `{$buile_table}` (\n"
					. implode(",\n", array_merge($fieldAttr, $key))
					. "\n)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '{$table}子表'";
				$ret = Common::execute($sql_drop);
				if($ret['code']==-1){
					return ['msg'=>'<h2>系统级错误：'.$ret['msg'].'</h2>','code'=>-1];
				}
				$ret2 = Common::execute($sql_create);
			   if($ret2['code']==-1){
					return ['msg'=>'<h2>系统级错误：'.$ret2['msg'].'</h2>','code'=>-1];
				}
				unset($fieldAttr);
				$i++;
			}else{
				return ['msg'=>'没有数据表跳过！','code'=>0];
			}
		}
		return ['msg'=>'创建成功！','code'=>0];
    }
	static function delDbbak($table,$data){
		$i = 1;
		foreach($data as $k=>$v){
			if($v['data']!='' && is_array($v['data']) && count($v['data'])>0){
				$buile_table = unit::gconfig('int_db_prefix').$table.'_d'.$i;
				try {
					$ret = Common::execute("DROP TABLE IF EXISTS `{$buile_table}_bak`");
				} catch (\Exception $e) {
					return ['code'=>1,'msg'=>'系统异常。'.$e->getMessage()];
				}
				
			}
			$i++;
		}
	}
}