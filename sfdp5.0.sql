/*
 Navicat Premium Data Transfer

 Source Server         : 本地服务器
 Source Server Type    : MySQL
 Source Server Version : 80025
 Source Host           : localhost:3306
 Source Schema         : sfdp5.0

 Target Server Type    : MySQL
 Target Server Version : 80025
 File Encoding         : 65001

 Date: 27/12/2021 16:49:06
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for wf_sfdp_btable
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_btable`;
CREATE TABLE `wf_sfdp_btable`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '表名称',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '表别名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_sfdp_btable
-- ----------------------------

-- ----------------------------
-- Table structure for wf_sfdp_design
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_design`;
CREATE TABLE `wf_sfdp_design`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `s_bill` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '表单名称',
  `s_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '表名',
  `s_db` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '数据库表名',
  `s_search` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `s_list` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `s_design` int(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 0 COMMENT '设计状态0：设计中|1：开始设计|2：启用部署',
  `s_look` int(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 0 COMMENT '是否锁定',
  `s_field` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `add_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `add_time` int NULL DEFAULT NULL,
  `s_db_bak` int NOT NULL DEFAULT 0 COMMENT '0：不存在备份表 1：存在',
  `s_type` int NOT NULL DEFAULT 0 COMMENT '设计类别',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_sfdp_design
-- ----------------------------

-- ----------------------------
-- Table structure for wf_sfdp_design_ver
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_design_ver`;
CREATE TABLE `wf_sfdp_design_ver`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` int NULL DEFAULT NULL COMMENT '关联ID',
  `s_bill` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '业务编号',
  `s_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '业务名称',
  `s_db` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '数据表名',
  `s_list` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '列表字段',
  `s_search` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '查询字段',
  `s_fun_id` int NULL DEFAULT NULL,
  `s_fun_ver` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '脚本版本',
  `s_field` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '字段JSON',
  `add_user` int NULL DEFAULT NULL COMMENT '用户id',
  `add_time` int NULL DEFAULT NULL COMMENT '创建时间',
  `status` int(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 0 COMMENT '版本状态0:停用|1:启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_sfdp_design_ver
-- ----------------------------

-- ----------------------------
-- Table structure for wf_sfdp_field
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_field`;
CREATE TABLE `wf_sfdp_field`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '编号',
  `sid` int NOT NULL COMMENT '版本编号',
  `field` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字段名称',
  `name_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字段别名',
  `zanwei` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '占位内容',
  `moren` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '默认内容',
  `is_request` int NULL DEFAULT NULL COMMENT '是否必填',
  `is_read` int NULL DEFAULT NULL,
  `length` int NULL DEFAULT NULL COMMENT '长度',
  `type_lx` int NOT NULL DEFAULT 0 COMMENT '选择类型',
  `type_data` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字段格式内容',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字段类型',
  `data` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '字段数据',
  `function` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '调用的函数方法',
  `is_list` int NULL DEFAULT 0 COMMENT '是否列表',
  `is_search` int NULL DEFAULT 0 COMMENT '是否查询',
  `search_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '查询类型',
  `update_time` int NULL DEFAULT NULL,
  `fid` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '字段id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 56 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_sfdp_field
-- ----------------------------

-- ----------------------------
-- Table structure for wf_sfdp_function
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_function`;
CREATE TABLE `wf_sfdp_function`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `bill` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '编号',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '标题',
  `fun_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '函数名',
  `function` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '函数SQL',
  `add_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '添加人',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  `status` int NOT NULL DEFAULT 0 COMMENT '0编辑中，1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '函数方法表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_sfdp_function
-- ----------------------------

-- ----------------------------
-- Table structure for wf_sfdp_modue
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_modue`;
CREATE TABLE `wf_sfdp_modue`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` int NULL DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '标题',
  `dbtable` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '数据表',
  `btn` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '按钮',
  `script` int NULL DEFAULT NULL COMMENT '脚本',
  `field_name` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `field` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `access` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `update_time` int NULL DEFAULT NULL,
  `order` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `show_type` smallint NULL DEFAULT 0 COMMENT '列表信息显示：0|普 1|树型',
  `show_fun` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '列表函数',
  `show_field` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '关联字段',
  `count_field` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '模块库' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_sfdp_modue
-- ----------------------------

-- ----------------------------
-- Table structure for wf_sfdp_script
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_script`;
CREATE TABLE `wf_sfdp_script`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` int NULL DEFAULT NULL COMMENT '关联ID',
  `s_bill` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '脚本编号',
  `s_fun` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '脚本代码',
  `add_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '添加用户',
  `add_time` int NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_sfdp_script
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
