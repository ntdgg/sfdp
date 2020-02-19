/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : tpfd3.0

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2020-02-19 17:12:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `wf_fb`
-- ----------------------------
DROP TABLE IF EXISTS `wf_fb`;
CREATE TABLE `wf_fb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '表单名称',
  `name` varchar(255) DEFAULT NULL COMMENT '表名',
  `file` varchar(255) DEFAULT NULL COMMENT '生成文件',
  `menu` int(11) NOT NULL DEFAULT '0',
  `flow` int(11) NOT NULL DEFAULT '0',
  `ziduan` longtext,
  `uid` varchar(255) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `status` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_fb
-- ----------------------------
INSERT INTO `wf_fb` VALUES ('1', 'testflow', 'test', 'all', '0', '1', null, null, '1582093871', '0');

-- ----------------------------
-- Table structure for `wf_fb_fun`
-- ----------------------------
DROP TABLE IF EXISTS `wf_fb_fun`;
CREATE TABLE `wf_fb_fun` (
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
-- Records of wf_fb_fun
-- ----------------------------

-- ----------------------------
-- Table structure for `wf_menu`
-- ----------------------------
DROP TABLE IF EXISTS `wf_menu`;
CREATE TABLE `wf_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_menu
-- ----------------------------
