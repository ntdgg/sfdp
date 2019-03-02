<?php
/**
 *+------------------
 * Tpfd 表单控件
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace tpdf;

use think\Db;
use think\facade\Config;
use think\Exception;

require_once Tp_DF . '/config/config.php';

class BuildTable{
	/**
     * 创建数据表
     */
    public function Btable($path, $pathTemplate, $fileName, $tableName, $code, $data)
    {
        $tableName = isset($data['table_name']) && $data['table_name'] ?
        $data['table_name'] : Config::get("database.prefix") . $tableName;
        $tableExist = false;// 判断表是否存在
        $ret = Db::query("SHOW TABLES LIKE '{$tableName}'");
        if ($ret && isset($ret[0])) {
            Db::execute("RENAME TABLE {$tableName} to {$tableName}_build_bak");
            $tableExist = true;
        }
        $auto_create_field = ['id', 'status', 'isdelete', 'create_time', 'update_time'];
        // 强制建表和不存在原表执行建表操作
        $fieldAttr = [];
        $key = [];
        if (in_array('id', $auto_create_field)) {
            $fieldAttr[] = tab(1) . "`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '{$data['title']}主键'";
        }
        foreach ($data['field'] as $field) {
            if (!in_array($field['name'], $auto_create_field)) {
                // 字段属性
                $fieldAttr[] = tab(1) . "`{$field['name']}` {$field['type']}"
                    . ($field['extra'] ? ' ' . $field['extra'] : '')
                    . (isset($field['not_null']) && $field['not_null'] ? ' NOT NULL' : '')
                    . (strtolower($field['default']) == null ? '' : " DEFAULT '{$field['default']}'")
                    . ($field['comment'] === '' ? '' : " COMMENT '{$field['comment']}'");
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
        $sql_drop = "DROP TABLE IF EXISTS `{$tableName}`";
        // 默认字符编码为utf8，表引擎默认InnoDB，其他都是默认
        $sql_create = "CREATE TABLE `{$tableName}` (\n"
            . implode(",\n", array_merge($fieldAttr, $key))
            . "\n)ENGINE=" . (isset($data['table_engine']) ? $data['table_engine'] : 'InnoDB')
            . " DEFAULT CHARSET=utf8 COMMENT '{$data['title']}'";
        try {
            Db::execute($sql_drop);
            Db::execute($sql_create);
            Db::execute("DROP TABLE IF EXISTS `{$tableName}_build_bak`");
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