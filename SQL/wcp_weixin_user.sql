/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : wcp

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-08-17 09:57:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wcp_weixin_user
-- ----------------------------
DROP TABLE IF EXISTS `wcp_weixin_user`;
CREATE TABLE `wcp_weixin_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号 主键 自增 ',
  `uid` varchar(255) NOT NULL COMMENT '用户编号，对应wcp_website_user表中的id',
  `openid` char(30) NOT NULL COMMENT '微信用户openid',
  `nickname` varchar(50) NOT NULL COMMENT '微信用户昵称',
  `sex` char(1) NOT NULL COMMENT '微信用户性别',
  `city` varchar(10) NOT NULL COMMENT '微信用户城市',
  `province` varchar(10) NOT NULL COMMENT '微信用户省份',
  `country` varchar(10) NOT NULL COMMENT '微信用户国家',
  `headimgurl` varchar(150) NOT NULL COMMENT '微信用户头像',
  `subscribe_time` int(10) NOT NULL COMMENT '用户关注时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 默认：1,其他： ',
  `type` tinyint(1) NOT NULL COMMENT '用户类型，保留字段',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信用户表';
