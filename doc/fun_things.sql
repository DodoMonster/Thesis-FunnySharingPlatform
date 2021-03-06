/*
Navicat MySQL Data Transfer

Source Server         : fun_thesis
Source Server Version : 50622
Source Host           : localhost:3306
Source Database       : fun_things

Target Server Type    : MYSQL
Target Server Version : 50622
File Encoding         : 65001

Date: 2017-05-07 00:50:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(255) NOT NULL,
  `cellphone` varchar(255) NOT NULL,
  `register_time` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'Dodo', '18814127439', '1494046788', 'e10adc3949ba59abbe56e057f20f883e');
INSERT INTO `admin` VALUES ('2', 'Momo', '18814127439', '1494046806', 'e10adc3949ba59abbe56e057f20f883e');

-- ----------------------------
-- Table structure for comment
-- ----------------------------
DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `things_id` int(11) DEFAULT NULL,
  `comment_time` bigint(20) DEFAULT NULL,
  `is_delete` enum('') DEFAULT NULL COMMENT '评论是否被删除，0为否，1为是',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of comment
-- ----------------------------
INSERT INTO `comment` VALUES ('1', '恭喜恭喜', '1', '2', '1490259051', null);
INSERT INTO `comment` VALUES ('2', '哈哈哈...好好笑', '3', '4', '1494047277', null);
INSERT INTO `comment` VALUES ('3', '多好的妹纸啊', '2', '5', '1494048338', null);

-- ----------------------------
-- Table structure for comment_user
-- ----------------------------
DROP TABLE IF EXISTS `comment_user`;
CREATE TABLE `comment_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(10) NOT NULL,
  `things_id` bigint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of comment_user
-- ----------------------------
INSERT INTO `comment_user` VALUES ('1', '1', '2');
INSERT INTO `comment_user` VALUES ('2', '3', '4');
INSERT INTO `comment_user` VALUES ('3', '2', '5');

-- ----------------------------
-- Table structure for favorite_things
-- ----------------------------
DROP TABLE IF EXISTS `favorite_things`;
CREATE TABLE `favorite_things` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(11) NOT NULL,
  `things_id` bigint(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of favorite_things
-- ----------------------------
INSERT INTO `favorite_things` VALUES ('1', '1', '2');

-- ----------------------------
-- Table structure for funny_things
-- ----------------------------
DROP TABLE IF EXISTS `funny_things`;
CREATE TABLE `funny_things` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(11) NOT NULL,
  `things_id` bigint(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of funny_things
-- ----------------------------
INSERT INTO `funny_things` VALUES ('1', '3', '5');
INSERT INTO `funny_things` VALUES ('2', '3', '2');

-- ----------------------------
-- Table structure for reply
-- ----------------------------
DROP TABLE IF EXISTS `reply`;
CREATE TABLE `reply` (
  `reply_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reply_user` int(10) NOT NULL,
  `replied_user` int(10) NOT NULL,
  `comment_id` int(10) NOT NULL,
  `reply_content` varchar(255) NOT NULL,
  `reply_time` bigint(20) NOT NULL,
  `reply_user_name` varchar(255) NOT NULL,
  `replied_user_name` varchar(255) NOT NULL,
  `things_id` int(10) NOT NULL,
  PRIMARY KEY (`reply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of reply
-- ----------------------------
INSERT INTO `reply` VALUES ('1', '2', '1', '1', '同喜同喜', '1490259549', '12345', 'DodoMonster', '2');
INSERT INTO `reply` VALUES ('2', '3', '2', '3', '我也是这么觉得的。。。', '1494048389', '走走停停看风景', 'littledodo', '5');

-- ----------------------------
-- Table structure for things
-- ----------------------------
DROP TABLE IF EXISTS `things`;
CREATE TABLE `things` (
  `things_id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `publish_time` bigint(20) NOT NULL,
  `things_content` varchar(255) DEFAULT NULL,
  `things_image` varchar(255) DEFAULT NULL,
  `funny_num` int(5) DEFAULT '0',
  `unfunny_num` int(5) DEFAULT '0',
  `user_id` int(5) DEFAULT NULL,
  `comment_num` int(5) DEFAULT '0' COMMENT '评论数',
  `is_approval` int(1) DEFAULT '0' COMMENT '是否审核通过，0为否，1为是',
  `favorite_num` int(5) DEFAULT '0' COMMENT '收藏数',
  `has_img` int(1) DEFAULT '0' COMMENT '是否带图片',
  `is_delete` enum('1','0') DEFAULT '0' COMMENT '是否被删除，0为否，1为是',
  PRIMARY KEY (`things_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of things
-- ----------------------------
INSERT INTO `things` VALUES ('1', '1483355483', '是的发送到平台了', '', '0', '0', '1', '0', '1', '0', '0', '0');
INSERT INTO `things` VALUES ('2', '1483355730', '第一条趣事', '', '1', '0', '2', '1', '1', '1', '0', '0');
INSERT INTO `things` VALUES ('3', '1483355836', '线上数据库坏了忧伤', '', '0', '0', '1', '0', '1', '0', '0', '0');
INSERT INTO `things` VALUES ('4', '1494046683', '下午抱着崽崽哄他觉，哼着歌，拍着屁股，他静静地趴在我的肩膀上，十分钟后准备把他放到小床上，结果人家扭过头来一脸懵逼的看着我，那炯炯有神的眼神好像在说：妈妈，你以为我睡了吗？我趴着可舒服了……', '', '1', '1', '2', '1', '1', '0', '0', '0');
INSERT INTO `things` VALUES ('5', '1494047189', '那时候我们公司新来了一个妹子，有天加班完，大家一起约着吃东西，人多车子不够坐，便问妹子开车没，妹子说:“开了，车小怕装不了人。”心想着哪怕qq也能装几个，便一起下去看，，，妹子开辆跑车，两坐的。。。好吧，妹子你赢了！！', '/uploads/things_img/100254029_1494047189.jpg', '1', '0', '3', '1', '1', '1', '0', '0');

-- ----------------------------
-- Table structure for unfunny_things
-- ----------------------------
DROP TABLE IF EXISTS `unfunny_things`;
CREATE TABLE `unfunny_things` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(11) NOT NULL,
  `things_id` bigint(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of unfunny_things
-- ----------------------------
INSERT INTO `unfunny_things` VALUES ('1', '3', '4');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_photo` varchar(100) NOT NULL,
  `register_time` bigint(20) DEFAULT NULL,
  `is_delete` enum('1','0') DEFAULT '0' COMMENT '是否删除，0为否，1为是',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'DodoMonster', 'e10adc3949ba59abbe56e057f20f883e', '/uploads/avatar/acatar.jpg', '1483352483', '0');
INSERT INTO `user` VALUES ('2', 'littledodo', 'e10adc3949ba59abbe56e057f20f883e', '/uploads/avatar/timg_1489808335.jpg', '1483315483', '0');
INSERT INTO `user` VALUES ('3', '走走停停看风景', 'e10adc3949ba59abbe56e057f20f883e', '/uploads/avatar/timg_1494047986.jpg', '1494047138', '0');
