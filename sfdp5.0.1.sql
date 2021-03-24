/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : sfdp5.0

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2021-03-24 11:14:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `wf_sfdp_btable`
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_btable`;
CREATE TABLE `wf_sfdp_btable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '表名称',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '表别名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of wf_sfdp_btable
-- ----------------------------

-- ----------------------------
-- Table structure for `wf_sfdp_design`
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_sfdp_design
-- ----------------------------

-- ----------------------------
-- Table structure for `wf_sfdp_design_ver`
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
-- Records of wf_sfdp_design_ver
-- ----------------------------

-- ----------------------------
-- Table structure for `wf_sfdp_field`
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_field`;
CREATE TABLE `wf_sfdp_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `sid` int(11) NOT NULL COMMENT '版本编号',
  `field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段名称',
  `name_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段别名',
  `zanwei` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '占位内容',
  `moren` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '默认内容',
  `is_request` int(11) DEFAULT NULL COMMENT '是否必填',
  `is_read` int(11) DEFAULT NULL,
  `length` int(11) DEFAULT NULL COMMENT '长度',
  `type_lx` int(11) NOT NULL DEFAULT '0' COMMENT '选择类型',
  `type_data` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段格式内容',
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段类型',
  `data` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段数据',
  `function` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '调用的函数方法',
  `is_list` int(11) DEFAULT '0' COMMENT '是否列表',
  `is_search` int(11) DEFAULT '0' COMMENT '是否查询',
  `search_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '查询类型',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of wf_sfdp_field
-- ----------------------------

-- ----------------------------
-- Table structure for `wf_sfdp_function`
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_function`;
CREATE TABLE `wf_sfdp_function` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fun_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `function` longtext COLLATE utf8_unicode_ci,
  `add_user` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0编辑中，1启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of wf_sfdp_function
-- ----------------------------

-- ----------------------------
-- Table structure for `wf_sfdp_modue`
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_modue`;
CREATE TABLE `wf_sfdp_modue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标题',
  `dbtable` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '数据表',
  `btn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '按钮',
  `script` int(11) DEFAULT NULL COMMENT '脚本',
  `field_name` longtext COLLATE utf8_unicode_ci,
  `field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access` longtext COLLATE utf8_unicode_ci,
  `update_time` int(11) DEFAULT NULL,
  `order` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of wf_sfdp_modue
-- ----------------------------

-- ----------------------------
-- Table structure for `wf_sfdp_script`
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

-- ----------------------------
-- Records of wf_sfdp_script
-- ----------------------------
