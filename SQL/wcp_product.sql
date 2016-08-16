/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : wcp

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-08-16 23:19:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wcp_product
-- ----------------------------
DROP TABLE IF EXISTS `wcp_product`;
CREATE TABLE `wcp_product` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号 主键 自增 ',
  `name` varchar(100) NOT NULL COMMENT '产品名称',
  `description` varchar(255) NOT NULL COMMENT '描述',
  `price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '价钱',
  `discount` decimal(11,2) NOT NULL COMMENT '折扣',
  `period_of_validity` int(11) NOT NULL COMMENT '有效期',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `modify_time` datetime NOT NULL COMMENT '最后修改时间',
  `modules` text NOT NULL COMMENT '模块功能列表 [0，1，3,....]',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '产品类型 1基础包 2扩展包',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否可用 默认2  1上线 2下线 0删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品表';
