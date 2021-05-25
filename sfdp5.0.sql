/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 80018
Source Host           : localhost:3306
Source Database       : sfdp5.0

Target Server Type    : MYSQL
Target Server Version : 80018
File Encoding         : 65001

Date: 2021-05-25 22:25:11
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wf_sfdp_btable
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_btable`;
CREATE TABLE `wf_sfdp_btable` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '表名称',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '表别名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='黑名单表';

-- ----------------------------
-- Records of wf_sfdp_btable
-- ----------------------------

-- ----------------------------
-- Table structure for wf_sfdp_design
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_design`;
CREATE TABLE `wf_sfdp_design` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `s_bill` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '表单名称',
  `s_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '表名',
  `s_db` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '数据库表名',
  `s_search` longtext CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '查询字段',
  `s_list` longtext CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '列表字段',
  `s_design` int(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '设计状态0：设计中|1：开始设计|2：启用部署',
  `s_look` int(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '是否锁定',
  `s_field` longtext CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '字段数据',
  `add_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '添加人',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `s_db_bak` int(11) NOT NULL DEFAULT '0' COMMENT '0：不存在备份表 1：存在',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='SFDP设计器主表';

-- ----------------------------
-- Records of wf_sfdp_design
-- ----------------------------
INSERT INTO `wf_sfdp_design` VALUES ('1', 'D202105255745', '测试', 'test', '[]', '[]', '2', '1', '{\"name\":\"测试\",\"name_db\":\"test\",\"tpfd_id\":\"SFDP5630501\",\"tpfd_btn\":[\"add\",\"View\"],\"tpfd_script\":\"\",\"tpfd_class\":\"\",\"list\":{\"Id215645315\":{\"tr\":\"Id215645315\",\"data\":{\"text_5647548\":{\"tpfd_id\":\"text_5647548\",\"tr_id\":\"Id215645315\",\"tpfd_name\":\"测试\",\"tpfd_db\":\"test\",\"tpfd_dblx\":\"varchar\",\"tpfd_dbcd\":\"255\",\"tpfd_list\":\"no\",\"tpfd_chaxun\":\"no\",\"tpfd_read\":\"1\",\"tpfd_must\":\"0\",\"tpfd_zanwei\":\"\",\"tpfd_moren\":\"\",\"td\":\"1\",\"td_type\":\"text\"}},\"type\":2}},\"sublist\":{},\"tpfd_time\":\"2021-05-25 21:56:30\",\"tpfd_ver\":\"v5.0\"}', 'Sys', '1621951024', '1');

-- ----------------------------
-- Table structure for wf_sfdp_design_ver
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_design_ver`;
CREATE TABLE `wf_sfdp_design_ver` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sid` int(11) DEFAULT NULL COMMENT '关联ID',
  `s_bill` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '业务编号',
  `s_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '业务名称',
  `s_db` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '数据表名',
  `s_list` longtext CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '列表字段',
  `s_search` longtext CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '查询字段',
  `s_fun_id` int(11) DEFAULT NULL COMMENT '脚本ID',
  `s_fun_ver` longtext CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '脚本版本',
  `s_field` longtext CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '字段JSON',
  `add_user` int(11) DEFAULT NULL COMMENT '用户id',
  `add_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` int(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '版本状态0:停用|1:启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='设计器版本表';

-- ----------------------------
-- Records of wf_sfdp_design_ver
-- ----------------------------
INSERT INTO `wf_sfdp_design_ver` VALUES ('1', '1', 'D202105259237', '测试', 'test', '[]', '[]', '0', '', '{\"name\":\"测试\",\"name_db\":\"test\",\"tpfd_id\":\"SFDP5630501\",\"tpfd_btn\":[\"add\",\"View\"],\"tpfd_script\":\"\",\"tpfd_class\":\"\",\"list\":{\"Id215645315\":{\"tr\":\"Id215645315\",\"data\":{\"text_5647548\":{\"tpfd_id\":\"text_5647548\",\"tr_id\":\"Id215645315\",\"tpfd_name\":\"测试\",\"tpfd_db\":\"test\",\"tpfd_dblx\":\"varchar\",\"tpfd_dbcd\":\"255\",\"tpfd_list\":\"no\",\"tpfd_chaxun\":\"no\",\"tpfd_read\":\"1\",\"tpfd_must\":\"0\",\"tpfd_zanwei\":\"\",\"tpfd_moren\":\"\",\"td\":\"1\",\"td_type\":\"text\"}},\"type\":2}},\"sublist\":{},\"tpfd_time\":\"2021-05-25 21:56:30\",\"tpfd_ver\":\"v5.0\"}', '1', '1621951031', '1');

-- ----------------------------
-- Table structure for wf_sfdp_field
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_field`;
CREATE TABLE `wf_sfdp_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `sid` int(11) NOT NULL COMMENT '版本编号',
  `field` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段名称',
  `name_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段别名',
  `zanwei` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '占位内容',
  `moren` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '默认内容',
  `is_request` int(11) DEFAULT NULL COMMENT '是否必填',
  `is_read` int(11) DEFAULT NULL,
  `length` int(11) DEFAULT NULL COMMENT '长度',
  `type_lx` int(11) NOT NULL DEFAULT '0' COMMENT '选择类型',
  `type_data` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段格式内容',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段类型',
  `data` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '字段数据',
  `function` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '调用的函数方法',
  `is_list` int(11) DEFAULT '0' COMMENT '是否列表',
  `is_search` int(11) DEFAULT '0' COMMENT '是否查询',
  `search_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '查询类型',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='字段库';

-- ----------------------------
-- Records of wf_sfdp_field
-- ----------------------------
INSERT INTO `wf_sfdp_field` VALUES ('1', '1', 'id', 'int', '主键', '', '', '0', '0', '11', '0', '', 'text', '', '', '0', '0', '', '1621952163');
INSERT INTO `wf_sfdp_field` VALUES ('2', '1', 'uid', 'int', '用户id', '', '', '0', '0', '11', '0', '', 'text', '', '', '0', '0', '', '1621952163');
INSERT INTO `wf_sfdp_field` VALUES ('3', '1', 'status', 'int', '审核状态', '', '', '0', '0', '11', '0', '', 'text', '', '', '0', '0', '', '1621952163');
INSERT INTO `wf_sfdp_field` VALUES ('4', '1', 'create_time', 'int', '新增时间', '', '', '0', '0', '11', '0', '', 'datetime', '', '', '0', '0', '', '1621952163');
INSERT INTO `wf_sfdp_field` VALUES ('5', '1', 'update_time', 'int', '更新时间', '', '', '0', '0', '11', '0', '', 'datetime', '', '', '0', '0', '', '1621952163');
INSERT INTO `wf_sfdp_field` VALUES ('6', '1', 'test', 'varchar', '测试', '', '', '0', '1', '255', '0', '\"\"', 'text', '', '', '1', '0', '0', '1621952163');

-- ----------------------------
-- Table structure for wf_sfdp_function
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_function`;
CREATE TABLE `wf_sfdp_function` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `bill` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '编号',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标题',
  `fun_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '函数名',
  `function` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '函数SQL',
  `add_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '添加人',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0编辑中，1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='函数方法表';

-- ----------------------------
-- Records of wf_sfdp_function
-- ----------------------------

-- ----------------------------
-- Table structure for wf_sfdp_modue
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_modue`;
CREATE TABLE `wf_sfdp_modue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标题',
  `dbtable` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '数据表',
  `btn` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '按钮',
  `script` int(11) DEFAULT NULL COMMENT '脚本',
  `field_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `field` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `access` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `update_time` int(11) DEFAULT NULL,
  `order` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='模块库';

-- ----------------------------
-- Records of wf_sfdp_modue
-- ----------------------------
INSERT INTO `wf_sfdp_modue` VALUES ('1', '1', '测试', 'test', 'add,View', null, '测试', 'test', null, '1621952163', null);

-- ----------------------------
-- Table structure for wf_sfdp_script
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_script`;
CREATE TABLE `wf_sfdp_script` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sid` int(11) DEFAULT NULL COMMENT '关联ID',
  `s_bill` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '脚本编号',
  `s_fun` longtext CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '脚本代码',
  `add_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '添加用户',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='脚本表';

-- ----------------------------
-- Records of wf_sfdp_script
-- ----------------------------
