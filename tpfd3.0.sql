/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : tpfd3.0

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-03-08 22:18:01
*/

SET FOREIGN_KEY_CHECKS=0;

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
-- Table structure for wf_sfdp_design
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_sfdp_design
-- ----------------------------
INSERT INTO `wf_sfdp_design` VALUES ('1', 'testflow', 'test', 'all', '0', '1', null, null, '{\"name\":\"请假\",\"name_db\":\"live\",\"tpfd_id\":\"SFDP591051\",\"tpfd_class\":\"table\",\"tpfd_fun\":\"\",\"tpfd_script\":\"\",\"list\":{\"Tr222517238\":{\"tr\":\"Tr222517238\",\"data\":{\"text_2524493\":{\"td\":\"1\",\"td_type\":\"text\",\"tpfd_id\":\"text_2524493\"}},\"type\":1},\"Tr222518600\":{\"tr\":\"Tr222518600\",\"data\":{\"checkboxes_2527220\":{\"td\":\"1\",\"td_type\":\"checkboxes\",\"tpfd_id\":\"checkboxes_2527220\"},\"radio_2543611\":{\"td\":\"2\",\"td_type\":\"radio\",\"tpfd_id\":\"radio_2543611\"}},\"type\":2},\"Tr222519576\":{\"tr\":\"Tr222519576\",\"data\":{\"dropdown_2530819\":{\"td\":\"1\",\"td_type\":\"dropdown\",\"tpfd_id\":\"dropdown_2530819\"},\"textarea_2534307\":{\"td\":\"2\",\"td_type\":\"textarea\",\"tpfd_id\":\"textarea_2534307\"},\"wenzi_2537477\":{\"td\":\"3\",\"td_type\":\"wenzi\",\"tpfd_id\":\"wenzi_2537477\"},\"date_2541216\":{\"td\":\"4\",\"td_type\":\"date\",\"tpfd_id\":\"date_2541216\"}},\"type\":4},\"Tr222520256\":{\"tr\":\"Tr222520256\",\"data\":{\"dropdown_254649\":{\"td\":\"1\",\"td_type\":\"dropdown\",\"tpfd_id\":\"dropdown_254649\"},\"radio_2548688\":{\"td\":\"2\",\"td_type\":\"radio\",\"tpfd_id\":\"radio_2548688\"},\"checkboxes_2550852\":{\"td\":\"3\",\"td_type\":\"checkboxes\",\"tpfd_id\":\"checkboxes_2550852\"}},\"type\":3},\"Tr222553768\":{\"tr\":\"Tr222553768\",\"data\":{\"html_2557986\":{\"td\":\"1\",\"td_type\":\"html\",\"tpfd_id\":\"html_2557986\"}},\"type\":1}},\"tpfd_time\":\"2020-03-03 21:59:10\",\"tpfd_ver\":\"v3.0\"}', null, '1582093871', '0');
INSERT INTO `wf_sfdp_design` VALUES ('2', '20200305224936', null, null, '0', '0', null, null, '{\"name\":\"111\",\"name_db\":\"\",\"tpfd_id\":\"SFDP3931419\",\"tpfd_class\":\"\",\"tpfd_fun\":\"\",\"tpfd_script\":\"\",\"list\":{\"Tr223933752\":{\"tr\":\"Tr223933752\",\"data\":{\"text_3938943\":{\"tpfd_id\":\"text_3938943\",\"tr_id\":\"Tr223933752\",\"tpfd_db\":\"test\",\"tpfd_name\":\"1111\",\"td\":\"1\",\"td_type\":\"text\"},\"text_3940290\":{\"tpfd_id\":\"text_3940290\",\"tr_id\":\"Tr223933752\",\"tpfd_db\":\"222\",\"tpfd_name\":\"2222\",\"td\":\"2\",\"td_type\":\"text\"}},\"type\":2},\"Tr223934843\":{\"tr\":\"Tr223934843\",\"data\":{\"checkboxes_394293\":{\"tpfd_id\":\"checkboxes_394293\",\"tr_id\":\"Tr223934843\",\"tpfd_db\":\"111\",\"tpfd_name\":\"222\",\"xx_type\":\"0\",\"tpfd_data\":[\"选项1\",\"选项2\"],\"tpfd_check\":\"1\",\"checkboxes_func\":\"func_aaaa\",\"td\":\"1\",\"td_type\":\"checkboxes\"},\"checkboxes_3943693\":{\"td\":\"2\",\"td_type\":\"checkboxes\",\"tpfd_id\":\"checkboxes_3943693\"},\"radio_3947737\":{\"tpfd_id\":\"radio_3947737\",\"tr_id\":\"Tr223934843\",\"tpfd_db\":\"333\",\"tpfd_name\":\"3333\",\"xx_type\":\"0\",\"tpfd_data\":[\"选项1\",\"选项2\",\"选项3\"],\"tpfd_check\":\"1\",\"checkboxes_func\":\"func_aaaa\",\"td\":\"3\",\"td_type\":\"radio\"}},\"type\":3},\"Tr223952492\":{\"tr\":\"Tr223952492\",\"data\":{\"dropdown_3955138\":{\"tpfd_id\":\"dropdown_3955138\",\"tr_id\":\"Tr223952492\",\"tpfd_db\":\"222\",\"tpfd_name\":\"55555\",\"xx_type\":\"0\",\"tpfd_data\":[\"选项1\",\"选项4\",\"选项5\",\"选项6\",\"选项7\"],\"checkboxes_func\":\"func_aaaa\",\"td\":\"1\",\"td_type\":\"dropdown\"},\"textarea_39582\":{\"tpfd_id\":\"textarea_39582\",\"tr_id\":\"Tr223952492\",\"tpfd_db\":\"333\",\"tpfd_name\":\"3333\",\"tpfd_zanwei\":\"\",\"tpfd_moren\":\"\",\"td\":\"2\",\"td_type\":\"textarea\"}},\"type\":2}},\"tpfd_time\":\"2020-03-06 22:39:31\",\"tpfd_ver\":\"v3.0\"}', null, '1583419776', '0');
INSERT INTO `wf_sfdp_design` VALUES ('3', 'data', null, null, '0', '0', '[{\"tpfd_id\":\"text_0339603\",\"tr_id\":\"Tr220028925\",\"tpfd_db\":\"test1\",\"tpfd_dbcd\":\"50\",\"tpfd_dblx\":\"int\",\"tpfd_name\":\"111\",\"tpfd_list\":\"yes\",\"tpfd_chaxun\":\"yes\",\"tpfd_show\":\"no\",\"td\":\"1\",\"td_type\":\"text\"}]', '[{\"tpfd_id\":\"text_0339603\",\"tr_id\":\"Tr220028925\",\"tpfd_db\":\"test1\",\"tpfd_dbcd\":\"50\",\"tpfd_dblx\":\"int\",\"tpfd_name\":\"111\",\"tpfd_list\":\"yes\",\"tpfd_chaxun\":\"yes\",\"tpfd_show\":\"no\",\"td\":\"1\",\"td_type\":\"text\"},{\"tpfd_id\":\"text_4135605\",\"tr_id\":\"Tr224127614\",\"tpfd_db\":\"data2\",\"tpfd_dbcd\":\"60\",\"tpfd_dblx\":\"int\",\"tpfd_name\":\"data2\",\"tpfd_list\":\"yes\",\"tpfd_chaxun\":\"no\",\"tpfd_show\":\"no\",\"td\":\"2\",\"td_type\":\"text\"},{\"tpfd_id\":\"text_404456\",\"tr_id\":\"Tr234040276\",\"tpfd_db\":\"data\",\"tpfd_dbcd\":\"1\",\"tpfd_dblx\":\"int\",\"tpfd_name\":\"\\u6570\\u636e\\u8d44\\u6599\",\"tpfd_list\":\"yes\",\"tpfd_chaxun\":\"no\",\"tpfd_show\":\"no\",\"td\":\"1\",\"td_type\":\"text\"}]', '{\"name\":\"name\",\"name_db\":\"test\",\"tpfd_id\":\"SFDP5536707\",\"tpfd_class\":\"\",\"tpfd_fun\":\"\",\"tpfd_script\":\"\",\"list\":{\"Tr220028925\":{\"tr\":\"Tr220028925\",\"data\":{\"text_0339603\":{\"tpfd_id\":\"text_0339603\",\"tr_id\":\"Tr220028925\",\"tpfd_db\":\"test1\",\"tpfd_dbcd\":\"50\",\"tpfd_dblx\":\"int\",\"tpfd_name\":\"111\",\"tpfd_list\":\"yes\",\"tpfd_chaxun\":\"yes\",\"tpfd_show\":\"no\",\"td\":\"1\",\"td_type\":\"text\"}},\"type\":1},\"Tr224127614\":{\"tr\":\"Tr224127614\",\"data\":{\"text_4131103\":{\"tpfd_id\":\"text_4131103\",\"tr_id\":\"Tr224127614\",\"tpfd_db\":\"222\",\"tpfd_dbcd\":\"50\",\"tpfd_dblx\":\"int\",\"tpfd_name\":\"222\",\"tpfd_list\":\"no\",\"tpfd_chaxun\":\"no\",\"tpfd_show\":\"no\",\"td\":\"1\",\"td_type\":\"text\"},\"text_4135605\":{\"tpfd_id\":\"text_4135605\",\"tr_id\":\"Tr224127614\",\"tpfd_db\":\"data2\",\"tpfd_dbcd\":\"60\",\"tpfd_dblx\":\"int\",\"tpfd_name\":\"data2\",\"tpfd_list\":\"yes\",\"tpfd_chaxun\":\"no\",\"tpfd_show\":\"no\",\"td\":\"2\",\"td_type\":\"text\"}},\"type\":2},\"Tr234040276\":{\"tr\":\"Tr234040276\",\"data\":{\"text_404456\":{\"tpfd_id\":\"text_404456\",\"tr_id\":\"Tr234040276\",\"tpfd_db\":\"data\",\"tpfd_dbcd\":\"1\",\"tpfd_dblx\":\"int\",\"tpfd_name\":\"数据资料\",\"tpfd_list\":\"yes\",\"tpfd_chaxun\":\"no\",\"tpfd_show\":\"no\",\"td\":\"1\",\"td_type\":\"text\"}},\"type\":1}},\"tpfd_time\":\"2020-03-07 21:55:36\",\"tpfd_ver\":\"v3.0\"}', null, null, '0');

-- ----------------------------
-- Table structure for wf_sfdp_function
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

-- ----------------------------
-- Table structure for wf_sfdp_link
-- ----------------------------
DROP TABLE IF EXISTS `wf_sfdp_link`;
CREATE TABLE `wf_sfdp_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sfdp_id` int(11) DEFAULT NULL,
  `work_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_sfdp_link
-- ----------------------------
INSERT INTO `wf_sfdp_link` VALUES ('1', '1', '1');
INSERT INTO `wf_sfdp_link` VALUES ('2', '2', '2');

-- ----------------------------
-- Table structure for wf_test
-- ----------------------------
DROP TABLE IF EXISTS `wf_test`;
CREATE TABLE `wf_test` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `test1` int(50) DEFAULT NULL COMMENT '111',
  `222` int(50) DEFAULT NULL COMMENT '222',
  `data2` int(60) DEFAULT NULL COMMENT 'data2',
  `data` int(1) DEFAULT NULL COMMENT '数据资料',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `uptime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='test';

-- ----------------------------
-- Records of wf_test
-- ----------------------------

-- ----------------------------
-- Table structure for wf_test_build_bak
-- ----------------------------
DROP TABLE IF EXISTS `wf_test_build_bak`;
CREATE TABLE `wf_test_build_bak` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `test1` int(50) DEFAULT NULL COMMENT '111',
  `222` int(50) DEFAULT NULL COMMENT '222',
  `data2` int(60) DEFAULT NULL COMMENT 'data2',
  `data` int(1) DEFAULT NULL COMMENT '数据资料',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `uptime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='test';

-- ----------------------------
-- Records of wf_test_build_bak
-- ----------------------------
INSERT INTO `wf_test_build_bak` VALUES ('1', '2', '3', '44', '55', '0', '0', '0', '0');
INSERT INTO `wf_test_build_bak` VALUES ('2', '2323', '23', '324', '123', '0', '0', '0', '0');
