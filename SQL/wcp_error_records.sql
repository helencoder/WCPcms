/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50625
Source Host           : localhost:3306
Source Database       : wcp

Target Server Type    : MYSQL
Target Server Version : 50625
File Encoding         : 65001

Date: 2016-04-19 15:05:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wcp_error_records
-- ----------------------------
DROP TABLE IF EXISTS `wcp_error_records`;
CREATE TABLE `wcp_error_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` text COMMENT '错误信息',
  `occur_time` datetime DEFAULT NULL COMMENT '错误发生时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
