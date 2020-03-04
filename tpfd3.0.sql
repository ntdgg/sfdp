/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : tpfd3.0

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-03-04 23:38:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wf_fb
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
INSERT INTO `wf_fb` VALUES ('1', 'testflow', 'test', 'all', '0', '1', '{\"name\":\"请假\",\"name_db\":\"live\",\"tpfd_id\":\"SFDP591051\",\"tpfd_class\":\"table\",\"tpfd_fun\":\"\",\"tpfd_script\":\"\",\"list\":{\"Tr222517238\":{\"tr\":\"Tr222517238\",\"data\":{\"text_2524493\":{\"td\":\"1\",\"td_type\":\"text\",\"tpfd_id\":\"text_2524493\"}},\"type\":1},\"Tr222518600\":{\"tr\":\"Tr222518600\",\"data\":{\"checkboxes_2527220\":{\"td\":\"1\",\"td_type\":\"checkboxes\",\"tpfd_id\":\"checkboxes_2527220\"},\"radio_2543611\":{\"td\":\"2\",\"td_type\":\"radio\",\"tpfd_id\":\"radio_2543611\"}},\"type\":2},\"Tr222519576\":{\"tr\":\"Tr222519576\",\"data\":{\"dropdown_2530819\":{\"td\":\"1\",\"td_type\":\"dropdown\",\"tpfd_id\":\"dropdown_2530819\"},\"textarea_2534307\":{\"td\":\"2\",\"td_type\":\"textarea\",\"tpfd_id\":\"textarea_2534307\"},\"wenzi_2537477\":{\"td\":\"3\",\"td_type\":\"wenzi\",\"tpfd_id\":\"wenzi_2537477\"},\"date_2541216\":{\"td\":\"4\",\"td_type\":\"date\",\"tpfd_id\":\"date_2541216\"}},\"type\":4},\"Tr222520256\":{\"tr\":\"Tr222520256\",\"data\":{\"dropdown_254649\":{\"td\":\"1\",\"td_type\":\"dropdown\",\"tpfd_id\":\"dropdown_254649\"},\"radio_2548688\":{\"td\":\"2\",\"td_type\":\"radio\",\"tpfd_id\":\"radio_2548688\"},\"checkboxes_2550852\":{\"td\":\"3\",\"td_type\":\"checkboxes\",\"tpfd_id\":\"checkboxes_2550852\"}},\"type\":3},\"Tr222553768\":{\"tr\":\"Tr222553768\",\"data\":{\"html_2557986\":{\"td\":\"1\",\"td_type\":\"html\",\"tpfd_id\":\"html_2557986\"}},\"type\":1}},\"tpfd_time\":\"2020-03-03 21:59:10\",\"tpfd_ver\":\"v3.0\"}', null, '1582093871', '0');

-- ----------------------------
-- Table structure for wf_fb_fun
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
-- Table structure for wf_menu
-- ----------------------------
DROP TABLE IF EXISTS `wf_menu`;
CREATE TABLE `wf_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_menu
-- ----------------------------
INSERT INTO `wf_menu` VALUES ('1', 'Test/index', 'TEST', null, null);

-- ----------------------------
-- Table structure for wf_sfdp_link
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_link`;
CREATE TABLE `wf_sfdp_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sfdp_id` int(11) DEFAULT NULL,
  `work_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_sfdp_link
-- ----------------------------
INSERT INTO `wf_sfdp_link` VALUES ('1', '1', '1');
