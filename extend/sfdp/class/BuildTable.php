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
use think\facade\Config;
use think\Exception;

require_once FILE_PATH . '/config/config.php';

class BuildTable{
	/**
     * 创建数据表
     */
    public function Btable($table,$data)
    {
        $tableName = Config::get("database.prefix") . $table;
        $tableExist = false;// 判断表是否存在
        $ret = Db::query("SHOW TABLES LIKE '{$tableName}'");
		
        if ($ret && isset($ret[0])) {
            Db::execute("RENAME TABLE {$tableName} to {$tableName}_build_bak");
            $tableExist = true;
	
        }
		
		
		//用户删除，则创建字段_bak
		//
	/* 
		①添加字段(add):
         alter table 表名 add 新字段名 数据类型；（添加到最后一列）
         alter table 表名 add 新字段名 数据类型 first;(添加到第一列)
         alter table 表名 add 新字段名 数据类型 after 字段名；（添加到指定位置之后）
         ②删除字段（drop）
         alter table 表名 drop 字段名；
         ③修改数据类型（modify）
         alter table 表名 modify 字段名 新数据类型；
         ④表重命名（remove）

          alter table 表名 remove 新表名
         ⑤表字段的重命名（change）
         alter table 表名 change 原名 新名 数据类型； */
		
        $auto_create_field = ['id', 'status', 'isdelete', 'create_time', 'update_time'];
        // 强制建表和不存在原表执行建表操作
        $fieldAttr = [];
        $key = [];
		
        if (in_array('id', $auto_create_field)) {
            $fieldAttr[] = tab(1) . "`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'";
        }
		
        foreach ($data as $field) {
            if (!in_array($field['tpfd_db'], $auto_create_field)) {
                // 字段属性
                $fieldAttr[] = tab(1) . "`{$field['tpfd_db']}` {$field['tpfd_dblx']}({$field['tpfd_dbcd']})"
                    . (isset($field['not_null']) && $field['not_null'] ? ' NOT NULL' : '')
                    . " DEFAULT NULL"
                    . ($field['tpfd_name'] === '' ? '' : " COMMENT '{$field['tpfd_name']}'");
            }
            // 索引
            if (isset($field['key']) && $field['key'] && $field['name'] != 'id') {
                $key[] = tab(1) . "KEY `{$field['name']}` (`{$field['name']}`)";
            }
        }
		
        // 默认自动创建主键为id
        $fieldAttr[] = tab(1) . "PRIMARY KEY (`id`)";
		$fieldAttr[] = tab(1) . "`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id'";
		$fieldAttr[] = tab(1) . "`status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态'";
		$fieldAttr[] = tab(1) . "`add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间'";
		$fieldAttr[] = tab(1) . "`uptime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'";
        $sql_drop = "DROP TABLE IF EXISTS `{$tableName}`";//删除数据表
        $sql_create = "CREATE TABLE `{$tableName}` (\n"
            . implode(",\n", array_merge($fieldAttr, $key))
            . "\n)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '{$table}'";
		
        try {
            Db::execute($sql_drop);
            Db::execute($sql_create);
          //  Db::execute("DROP TABLE IF EXISTS `{$tableName}_build_bak`");
        } catch (\Exception $e) {
            // 模拟事务操作，滚回原表
            if ($tableExist) {
                Db::execute("RENAME TABLE {$tableName}_build_bak to {$tableName}");
            }
            throw new Exception($e->getMessage());
        }
		return ['info'=>'创建成功！','code'=>0];
    }
}