/*
 *+------------------
 * SFDP 5.0 数据库文件
 *+------------------
 * Copyright (c) 2006~2025 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- 主要设计表
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_design`;
CREATE TABLE `wf_sfdp_design` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_bill` varchar(255) DEFAULT NULL COMMENT '表单名称',
  `s_title` varchar(255) DEFAULT NULL COMMENT '表名',
  `s_db` varchar(255) DEFAULT NULL COMMENT '数据库表名',
  `s_search` longtext,
  `s_list` longtext,
  `s_design` int(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '设计状态0：设计中|1：开始设计|2：启用部署',
  `s_look` int(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '是否锁定',
  `s_field` longtext,
  `add_user` varchar(255) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `s_db_bak` int(1) NOT NULL DEFAULT '0' COMMENT '0：不存在备份表 1：存在',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- 设计版本库
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_design_ver`;
CREATE TABLE `wf_sfdp_design_ver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) DEFAULT NULL COMMENT '关联ID',
  `s_bill` varchar(255) DEFAULT NULL COMMENT '业务编号',
  `s_name` varchar(255) NOT NULL COMMENT '业务名称',
  `s_db` varchar(255) NOT NULL COMMENT '数据表名',
  `s_list` longtext COMMENT '列表字段',
  `s_search` longtext COMMENT '查询字段',
  `s_fun_id` int(11) DEFAULT NULL,
  `s_fun_ver` longtext COMMENT '脚本版本',
  `s_field` longtext COMMENT '字段JSON',
  `add_user` int(2) DEFAULT NULL COMMENT '用户id',
  `add_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` int(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '版本状态0:停用|1:启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- 函数表
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_function`;
CREATE TABLE `wf_sfdp_function` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `fun_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `function` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `add_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0编辑中，1启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- 脚本表
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_script`;
CREATE TABLE `wf_sfdp_script` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) DEFAULT NULL COMMENT '关联ID',
  `s_bill` varchar(255) DEFAULT NULL COMMENT '脚本编号',
  `s_fun` longtext COMMENT '脚本代码',
  `add_user` varchar(255) DEFAULT NULL COMMENT '添加用户',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
