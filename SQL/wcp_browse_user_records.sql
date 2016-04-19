/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50625
Source Host           : localhost:3306
Source Database       : wcp

Target Server Type    : MYSQL
Target Server Version : 50625
File Encoding         : 65001

Date: 2016-04-19 14:49:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wcp_browse_user_records
-- ----------------------------
DROP TABLE IF EXISTS `wcp_browse_user_records`;
CREATE TABLE `wcp_browse_user_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) DEFAULT NULL COMMENT '用户浏览IP',
  `device` varchar(255) DEFAULT NULL COMMENT '用户浏览设备',
  `phpsessid` varchar(255) DEFAULT NULL COMMENT '用户浏览标记',
  `browse_time` datetime DEFAULT NULL COMMENT '用户浏览时间',
  `user_id` int(11) DEFAULT NULL COMMENT '注册用户ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
