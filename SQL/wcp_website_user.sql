/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : wcp

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-08-17 09:38:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wcp_website_user
-- ----------------------------
DROP TABLE IF EXISTS `wcp_website_user`;
CREATE TABLE `wcp_website_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL COMMENT '用户名，唯一',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `openid` char(30) DEFAULT NULL,
  `email` varchar(255) NOT NULL COMMENT '用户邮箱，唯一',
  `type` tinyint(4) DEFAULT '3' COMMENT '用户权限，管理员：0，付费用户：1，试用用户：2，游客：3',
  `create_time` datetime DEFAULT NULL COMMENT '用户创建时间',
  `modify_time` datetime DEFAULT NULL COMMENT '用户修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
