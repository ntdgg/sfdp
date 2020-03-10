/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : tpfd3.0

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2020-03-10 10:41:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `wf_sfdp_design`
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_design`;
CREATE TABLE `wf_sfdp_design` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '表单名称',
  `name` varchar(255) DEFAULT NULL COMMENT '表名',
  `file` varchar(255) DEFAULT NULL COMMENT '生成文件',
  `menu` int(11) NOT NULL DEFAULT '0',
  `flow` int(11) NOT NULL DEFAULT '0',
  `chaxun` longtext,
  `list` longtext,
  `ziduan` longtext,
  `uid` varchar(255) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `status` int(11) unsigned NOT NULL DEFAULT '0',
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
-- Table structure for `wf_sfdp_function`
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_function`;
CREATE TABLE `wf_sfdp_function` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT NULL,
  `sql` longtext,
  `name` varchar(255) DEFAULT NULL,
  `zdname` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_sfdp_function
-- ----------------------------
