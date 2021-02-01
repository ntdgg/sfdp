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


class BuildTable{
	/**
     * 创建数据表
     */
    static function Btable($table,$data,$btn)
    {
		if (in_array($table, unit::gconfig('black_table'))) {
			return ['msg'=>'该数据表不允许创建','code'=>1];
        }
        $tableName = unit::gconfig('int_db_prefix') . $table;
        $tableExist = false;// 判断表是否存在
        $ret = Common::query("SHOW TABLES LIKE '{$tableName}'");
		self::hasDbbak($table);
        if ($ret && isset($ret[0])) {
            Common::execute("RENAME TABLE {$tableName} to {$tableName}_bak");
            $tableExist = true;
        }
        $auto_create_field = ['id', 'status', 'create_time', 'update_time'];
        // 强制建表和不存在原表执行建表操作
        $fieldAttr = [];
        $key = [];
        if (in_array('id', $auto_create_field)) {
            $fieldAttr[] = unit::tab(1) . "`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'";
        }
        foreach ($data as $field) {
            if (!in_array($field['tpfd_db'], $auto_create_field)) {
				if($field['tpfd_dblx']=='datetime'||$field['tpfd_dblx']=='longtext'){
					$fieldAttr[] = unit::tab(1) . "`{$field['tpfd_db']}` {$field['tpfd_dblx']} COMMENT '{$field['tpfd_name']}'";
					}else{
					$fieldAttr[] = unit::tab(1) . "`{$field['tpfd_db']}` {$field['tpfd_dblx']}({$field['tpfd_dbcd']}) DEFAULT NULL COMMENT '{$field['tpfd_name']}'";
				}
            }
        }
		$fieldAttr[] = unit::tab(1) . "`uid` int(10) DEFAULT '0' COMMENT '用户id'";
		$fieldAttr[] = unit::tab(1) . "`status` int(10)  DEFAULT '0' COMMENT '审核状态'";
		$fieldAttr[] = unit::tab(1) . "`create_time` int(10)  DEFAULT '0' COMMENT '新增时间'";
		$fieldAttr[] = unit::tab(1) . "`update_time` int(10)  DEFAULT '0' COMMENT '更新时间'";
		if((in_array('WorkFlow',$btn)) || (in_array('Status',$btn))){
			$fieldAttr[] = unit::tab(1) . "`uptime` int(10)  DEFAULT '0' COMMENT '工作流调用更新时间'";
		}
		$fieldAttr[] = unit::tab(1) . "PRIMARY KEY (`id`)";
        $sql_drop = "DROP TABLE IF EXISTS `{$tableName}`";//删除数据表
		if((in_array('WorkFlow',$btn)) || (in_array('Status',$btn))){
			$sql_create = "CREATE TABLE `{$tableName}` (\n"
				. implode(",\n", array_merge($fieldAttr, $key))
				. "\n)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '[work]{$table}'";
        }else{
			$sql_create = "CREATE TABLE `{$tableName}` (\n"
				. implode(",\n", array_merge($fieldAttr, $key))
				. "\n)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '{$table}'";
			
		}
        $ret = Common::execute($sql_drop);
		if($ret['code']==-1){
			return ['msg'=>'<h2>系统级错误：'.$ret['msg'].'</h2>','code'=>-1];
		}
        $ret2 = Common::execute($sql_create);
       if($ret2['code']==-1){
			return ['msg'=>'<h2>系统级错误：'.$ret2['msg'].'</h2>','code'=>-1];
		}
		return ['msg'=>'创建成功！','code'=>0];
    }
	static function hasDbbak($table){
        $tableName = unit::gconfig('int_db_prefix') . $table;
		$ret_bak = Common::query("SHOW TABLES LIKE '{$tableName}_bak'");
		if ($ret_bak && isset($ret_bak[0])) { 
			return ['code'=>1,'msg'=>'备份数据表已经存在，请先删除！'];
		}
	}
	static function delDbbak($table){
        $tableName = unit::gconfig('int_db_prefix') . $table;
		$ret_bak = Common::query("SHOW TABLES LIKE '{$tableName}_bak'");
		if ($ret_bak && isset($ret_bak[0])) { 
			try {
				$ret = Common::execute("DROP TABLE IF EXISTS `{$tableName}_bak`");
			} catch (\Exception $e) {
				return ['code'=>1,'msg'=>'系统异常。'.$e->getMessage()];
			}
			return ['code'=>0,'msg'=>'备份表已经删除！'];
		}else{
			return ['code'=>1,'msg'=>'备份表不存在！'];
		}
		
	}
}