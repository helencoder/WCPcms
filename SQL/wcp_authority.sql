/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : wcp

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-08-16 23:19:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wcp_authority
-- ----------------------------
DROP TABLE IF EXISTS `wcp_authority`;
CREATE TABLE `wcp_authority` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号 主键 自增 ',
  `uid` varchar(255) NOT NULL COMMENT '用户编号',
  `product_id` bigint(20) NOT NULL COMMENT '产品编号',
  `order_id` bigint(20) NOT NULL COMMENT '产生权限时的订单编号',
  `p_start_time` datetime NOT NULL COMMENT '使用开始时间',
  `p_end_time` datetime NOT NULL COMMENT '使用结束时间',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `modify_time` datetime NOT NULL COMMENT '最后修改时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 默认：1 ',
  `type` tinyint(1) NOT NULL COMMENT '1试用,2购买',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限表';
