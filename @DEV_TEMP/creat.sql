/*
 Navicat Premium Data Transfer

 Source Server         : 139.196.229.154
 Source Server Type    : MariaDB
 Source Server Version : 100334
 Source Host           : 139.196.229.154:3306
 Source Schema         : starfission

 Target Server Type    : MariaDB
 Target Server Version : 100334
 File Encoding         : 65001

 Date: 29/07/2022 14:54:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for pre_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `pre_admin_log`;
CREATE TABLE `pre_admin_log`  (
  `id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID',
  `auid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户AUid',
  `username` char(24)  NOT NULL DEFAULT '',
  `dateline` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `action` char(15)  NOT NULL DEFAULT '',
  `url` text  NOT NULL,
  `method` char(15)  NOT NULL DEFAULT '',
  `request` text  NOT NULL,
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_auid`(`auid`) USING BTREE,
  INDEX `idx_dateline`(`dateline`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台访问日志';

-- ----------------------------
-- Table structure for pre_admin_note
-- ----------------------------
DROP TABLE IF EXISTS `pre_admin_note`;
CREATE TABLE `pre_admin_note`  (
  `id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID',
  `auid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户AUid',
  `notetext` text  NULL DEFAULT NULL COMMENT '便签本文',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_auid`(`auid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台用户便签';

-- ----------------------------
-- Table structure for pre_admin_perm
-- ----------------------------
DROP TABLE IF EXISTS `pre_admin_perm`;
CREATE TABLE `pre_admin_perm`  (
  `auid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '后台用户UID',
  `modkeyword` char(255)  NOT NULL DEFAULT '' COMMENT '模块权限关键字',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`auid`, `modkeyword`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台权限表';

-- ----------------------------
-- Table structure for pre_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `pre_admin_user`;
CREATE TABLE `pre_admin_user`  (
  `auid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` char(24)  NOT NULL DEFAULT '',
  `password` char(32)  NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(24)  NOT NULL DEFAULT '' COMMENT '密码混淆码',
  `dateline` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `permission` longtext  NOT NULL COMMENT '权限列表',
  `token` char(255)  NOT NULL DEFAULT '' COMMENT '用户授权令牌',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`auid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台管理员表';

-- ----------------------------
-- Records of pre_admin_user
-- ----------------------------
BEGIN;
INSERT INTO `pre_admin_user` VALUES (1, 'admin', 'd6812312fcbbb7f1026a9f45315a725e', '4345', 0, 'a:1:{s:9:\"{allperm}\";b:1;}', '5c52by3GVoUweSfdpF80uy75ggBzGt2hg4sv0LbaLooslxaMICzr26BnuiDS472dA[c]MDh8ZtGQsOS9Gumb7rP2Jj3AyciBCjbbRpfahgpndlKlaZ', '0000-00-00 00:00:00', '0001-01-01 00:00:00', '0001-01-01 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_banner
-- ----------------------------
DROP TABLE IF EXISTS `pre_banner`;
CREATE TABLE `pre_banner`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '推荐ID',
  `title` char(255)  NOT NULL DEFAULT '' COMMENT '标题',
  `title_pic` char(255)  NULL DEFAULT NULL COMMENT '标题图',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型 0 网址链接 1:NFT商品  2:公告',
  `type_data` char(255)  NOT NULL DEFAULT '' COMMENT '类型关联数据(链接 或者 ID)',
  `start_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '开始时间 - 每日零时',
  `end_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '结束时间- 当日某个时刻时间',
  `start_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间 - 每日零时',
  `end_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间- 当日某个时刻时间',
  `weight` mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_weight`(`weight`) USING BTREE,
  INDEX `idx_start_at`(`start_at`) USING BTREE,
  INDEX `idx_start_time`(`start_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'banner推荐';

-- ----------------------------
-- Table structure for pre_blockchain
-- ----------------------------
DROP TABLE IF EXISTS `pre_blockchain`;
CREATE TABLE `pre_blockchain`  (
  `blockchain_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '区块链ID',
  `blockchain_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '区块链名字',
  `blockchain_key` varchar(255)  NOT NULL DEFAULT '' COMMENT '区块链key',
  `blockchain_icon` varchar(255)  NOT NULL DEFAULT '' COMMENT '区块链ICON',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`blockchain_id`) USING BTREE,
  INDEX `idx_is_deleted`(`is_deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '区块链类型表';

-- ----------------------------
-- Records of pre_blockchain
-- ----------------------------
BEGIN;
INSERT INTO `pre_blockchain` VALUES (1, '树图', 'keykey', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (2, '以太坊', 'key2', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_brand
-- ----------------------------
DROP TABLE IF EXISTS `pre_brand`;
CREATE TABLE `pre_brand`  (
  `brand_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '品牌ID',
  `brand_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '品牌方名称',
  `brand_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '版权方图片',
  `brand_full_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '版权方完整文字',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `order` bigint(20) NOT NULL DEFAULT 0 COMMENT '排序',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`brand_id`) USING BTREE,
  INDEX `idx_is_deleted`(`is_deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品品牌方';

-- ----------------------------
-- Records of pre_brand
-- ----------------------------
BEGIN;
INSERT INTO `pre_brand` VALUES (1, '品牌方测试1', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_contract_metadata
-- ----------------------------
DROP TABLE IF EXISTS `pre_contract_metadata`;
CREATE TABLE `pre_contract_metadata`  (
  `contract_metadata_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `contract_metadata_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应的名字',
  `contract_metadata_description` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应的描述',
  `contract_metadata_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应的固定图片',
  `contract_metadata_animation_url` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应播放媒体地址',
  `goods_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '购买商品',
  `order` bigint(20) NOT NULL DEFAULT 0 COMMENT '排序',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'metadata状态 0：失效,1：生效中',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`contract_metadata_id`) USING BTREE,
  INDEX `idx_status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品 合约metadata 表';

-- ----------------------------
-- Records of pre_contract_metadata
-- ----------------------------
BEGIN;
INSERT INTO `pre_contract_metadata` VALUES (1, '3333', '', '', '', 0, 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_contract_type
-- ----------------------------
DROP TABLE IF EXISTS `pre_contract_type`;
CREATE TABLE `pre_contract_type`  (
  `contract_type_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '合约类型ID',
  `contract_type_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约类型名字',
  `contract_type_key` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约类型Key',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`contract_type_id`) USING BTREE,
  INDEX `idx_is_deleted`(`is_deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '合约类型表';

-- ----------------------------
-- Records of pre_contract_type
-- ----------------------------
BEGIN;
INSERT INTO `pre_contract_type` VALUES (1, '合约一', 'c1', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (3, '3', '3', 0, '2022-07-22 14:00:32', '2022-07-22 14:00:32', '0000-00-00 00:00:00'), (4, '666', '6666', 0, '2022-07-22 14:02:25', '2022-07-22 14:02:25', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_copyright
-- ----------------------------
DROP TABLE IF EXISTS `pre_copyright`;
CREATE TABLE `pre_copyright`  (
  `copyright_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '版权方ID',
  `copyright_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '版权方名字',
  `copyright_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '版权方图片',
  `copyright_full_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '版权方完整文字',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `order` bigint(20) NOT NULL DEFAULT 0 COMMENT '排序',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`copyright_id`) USING BTREE,
  INDEX `idx_is_deleted`(`is_deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品版权方';

-- ----------------------------
-- Records of pre_copyright
-- ----------------------------
BEGIN;
INSERT INTO `pre_copyright` VALUES (1, '版权方名字', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_goods
-- ----------------------------
DROP TABLE IF EXISTS `pre_goods`;
CREATE TABLE `pre_goods`  (
  `goods_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `goods_sn` char(30)  NOT NULL DEFAULT '' COMMENT '货号',
  `auid` bigint(20) NOT NULL DEFAULT 0 COMMENT '后端发布用户ID',
  `category_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '一级分类',
  `category_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '一级分类',
  `copyright_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '版权方ID',
  `copyright_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '版权方名字',
  `brand_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '品牌ID',
  `brand_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '品牌方名称',
  `release_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '发行方ID',
  `release_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '发行方名称',
  `goods_name` varchar(255)  NOT NULL COMMENT '商品名称',
  `goods_price` decimal(20, 2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品价格',
  `goods_market_price` decimal(20, 2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '市场价',
  `goods_express_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '运费类型 0:免运费，1:全国运费一个价, 2:根据地区和货物重量单独计算）',
  `goods_express_price` decimal(20, 2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品运费 0 免运费',
  `goods_url` varchar(256)  NOT NULL DEFAULT '' COMMENT '连接关键词(英文)',
  `goods_weight` bigint(20) NOT NULL DEFAULT 0 COMMENT '商品单位重量（g）',
  `goods_stock` bigint(20) NOT NULL DEFAULT 0 COMMENT '当前库存',
  `goods_total_stock` bigint(20) NOT NULL DEFAULT 0 COMMENT '历史总库存',
  `goods_title_pic` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品标题图',
  `goods_thumb_pic` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `goods_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品封面图',
  `goods_ar` varchar(500)  NOT NULL DEFAULT '' COMMENT 'ar 模型',
  `goods_ar_image` varchar(500)  NOT NULL DEFAULT '' COMMENT 'ar加载图',
  `goods_tags` varchar(255)  NOT NULL DEFAULT '' COMMENT '标签 使用英文逗号间隔 ',
  `goods_body` text  NOT NULL DEFAULT '' COMMENT '商品内容',
  `goods_body_mobile` text  NOT NULL DEFAULT '' COMMENT '商品内容移动版',
  `blockchain_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '区块链类型',
  `blockchain_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '区块链名字',
  `blockchain_key` varchar(255)  NOT NULL DEFAULT '' COMMENT '区块链key',
  `blockchain_address` varchar(256)  NULL DEFAULT '' COMMENT '区块链地址',
  `goods_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0：实物商品，1：数字藏品，2：实物礼包， 3：数字藏品-盲盒',
  `buy_num_limit` bigint(20) NOT NULL DEFAULT 1 COMMENT '限购数量',
  `contract_metadata_id` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata类型ID',
  `contract_type` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约类型 ',
  `contract_network` varchar(255)  NOT NULL DEFAULT '' COMMENT '所在的网络(树图直接网络ID)',
  `contract_tokenuri_url_domain` varchar(255)  NOT NULL DEFAULT '' COMMENT 'tokenuri 访问地址域名',
  `contract_tokenuri_url_pre` varchar(255)  NOT NULL DEFAULT '' COMMENT 'tokenuri 访问地址前缀',
  `contract_keystore_path` varchar(255)  NOT NULL DEFAULT '' COMMENT 'keystore地址',
  `is_hide` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否隐藏显示',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '商品状态（0下架,1上架）',
  `released_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '发行时间(时间戳)',
  `sale_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '销售时间(时间戳)',
  `released_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '发行时间',
  `sale_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '销售时间',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  `weight` bigint(20) NOT NULL DEFAULT 0 COMMENT '排序权重',
  PRIMARY KEY (`goods_id`) USING BTREE,
  INDEX `idx_category_id`(`category_id`, `weight`, `is_deleted`) USING BTREE,
  INDEX `idx_copyright_id`(`copyright_id`, `weight`, `is_deleted`) USING BTREE,
  INDEX `idx_brand_id`(`brand_id`, `weight`, `is_deleted`) USING BTREE,
  INDEX `idx_release_id`(`release_id`, `weight`, `is_deleted`) USING BTREE,
  INDEX `idx_goods_sn`(`goods_sn`, `is_deleted`) USING BTREE,
  INDEX `idx_status_order`(`status`, `weight`, `is_deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品表';

-- ----------------------------
-- Records of pre_goods
-- ----------------------------
BEGIN;
INSERT INTO `pre_goods` VALUES (1, 'E2525225522', 1, 1, ' 测试分类', 1, '版权方名字', 1, '品牌方测试1', 1, '发行方测试1', '测试NFT', 50.00, 50.00, 0, 0.00, '', 0, 92, 5000, '', '', 'https://assets.starfission.cn/admin/static/JeyfRMitmA.jpg', 'https://assets.starfission.cn/admin/static/Zkrbdrr6Yb.jpg', '', '', '', '<p>B%BD%E9%98%BF%E8%8B%8F%E9%98%BF%E8%8B%8F%2010.png&quot;/&gt;<br/></p><p><br/></p><p>《平头哥》系列3D数字藏品全网首发。该系列数字藏品即支持AR场景使用功能，平头哥的伙伴们即将打破次元壁垒出现在你的眼前。该藏品限量5000份，先到先得。</p><p><br/>《平头哥》是一部轻松解压的动物主题短视 频动画，讲述了平头哥和朋友们由于性格迥异闹出的各种趣事。始终向观众传递着积极 向上的乐观精神。</p><p><br/>2022年《平头哥》逐渐成为国内原创动漫短视频头部作品，短视频播放量超25亿次，全网粉丝1500W+。</p>', 1, '树图', 'keykey', '', 2, 100, '1', '1', '', '', '', '', 0, 0, 0, -62170013143, -62170013143, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2022-07-25 00:31:11', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0);
COMMIT;

-- ----------------------------
-- Table structure for pre_goods_category
-- ----------------------------
DROP TABLE IF EXISTS `pre_goods_category`;
CREATE TABLE `pre_goods_category`  (
  `category_id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `category_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '分类名称',
  `category_name_en` varchar(255)  NOT NULL DEFAULT '' COMMENT '英文分类名',
  `is_hide` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否隐藏显示',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `order` bigint(20) NOT NULL DEFAULT 0 COMMENT '排序',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品分类表';

-- ----------------------------
-- Records of pre_goods_category
-- ----------------------------
BEGIN;
INSERT INTO `pre_goods_category` VALUES (1, ' 测试分类', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_member
-- ----------------------------
DROP TABLE IF EXISTS `pre_member`;
CREATE TABLE `pre_member`  (
  `uid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户UID',
  `nickname` char(24)  NOT NULL DEFAULT '' COMMENT '用户昵称',
  `avatar` char(255)  NOT NULL DEFAULT '' COMMENT '头像',
  `mobile` char(255)  NOT NULL DEFAULT '' COMMENT '电话号码',
  `name` char(255)  NOT NULL DEFAULT '' COMMENT '真实姓名',
  `idcard` char(255)  NOT NULL DEFAULT '' COMMENT '身份证号',
  `is_validate` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否身份验证',
  `openid` varchar(255)  NOT NULL DEFAULT '' COMMENT 'openid',
  `unionid` varchar(255)  NOT NULL DEFAULT '' COMMENT 'unionid',
  `gender` varchar(255)  NOT NULL DEFAULT '' COMMENT '性别',
  `language` varchar(255)  NOT NULL DEFAULT '' COMMENT '语言',
  `city` varchar(255)  NOT NULL DEFAULT '' COMMENT '城市',
  `province` varchar(255)  NOT NULL DEFAULT '' COMMENT '省份',
  `country` varchar(255)  NOT NULL DEFAULT '' COMMENT '国家',
  `avatar_url` varchar(255)  NOT NULL DEFAULT '' COMMENT '微信的头像',
  `session_key` varchar(255)  NOT NULL DEFAULT '' COMMENT 'session_key',
  `token` char(255)  NOT NULL DEFAULT '' COMMENT '用户TOKEN',
  `weixin_token` char(255)  NOT NULL DEFAULT '' COMMENT '用户微信TOKEN',
  `change_name_num` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户昵称剩余修改次数',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`uid`) USING BTREE,
  INDEX `idx_openid`(`openid`) USING BTREE,
  INDEX `idx_unionid`(`unionid`) USING BTREE,
  INDEX `idx_mobile`(`mobile`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员表';

-- ----------------------------
-- Records of pre_member
-- ----------------------------
BEGIN;
INSERT INTO `pre_member` VALUES (1, '星河探索者#1', '', '18621853099', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 99, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (2, '星河探索者#2', 'https://assets.starfission.cn/images/avatars/1.png', '18621853096', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 99, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (3, '星河探索者#3', 'https://assets.starfission.cn/images/avatars/1.png', '18621853098', '杨家亮', '52010319880604441x', 1, '', '', '', '', '', '', '', '', '', '', '', 99, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (4, '星河探索者#4', 'https://assets.starfission.cn/images/avatars/1.png', '17130849520', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 99, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (5, '星河探索者#5', 'https://assets.starfission.cn/images/avatars/1.png', '13598407991', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 99, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (6, '星河探索者#6', 'https://assets.starfission.cn/images/avatars/1.png', '13826180376', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 99, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (7, '昔年', 'https://assets.starfission.cn/images/avatars/1.png', '15207191726', '宋响林', '421122200105087317', 1, '', '', '', '', '', '', '', '', '', '', '', 96, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_member_address
-- ----------------------------
DROP TABLE IF EXISTS `pre_member_address`;
CREATE TABLE `pre_member_address`  (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `link_phone` varchar(32)  NOT NULL DEFAULT '' COMMENT '收货手机',
  `link_man` varchar(255)  NOT NULL DEFAULT '' COMMENT '收货联系人',
  `link_address` varchar(255)  NOT NULL DEFAULT '' COMMENT '收货人详细地址',
  `zip_code` varchar(20)  NULL DEFAULT NULL COMMENT '邮编',
  `province_id` int(11) NOT NULL DEFAULT 0 COMMENT '省ID',
  `province` varchar(100)  NOT NULL DEFAULT '' COMMENT '省',
  `city_id` int(11) NOT NULL DEFAULT 0 COMMENT '市ID',
  `city` varchar(100)  NOT NULL DEFAULT '' COMMENT '市',
  `area_id` int(11) NOT NULL DEFAULT 0 COMMENT '区县ID',
  `area` varchar(100)  NOT NULL DEFAULT '' COMMENT '区县',
  `county_id` int(11) NOT NULL DEFAULT 0 COMMENT '国家ID',
  `weight` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `is_default` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_uid`(`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户收货地址';

-- ----------------------------
-- Records of pre_member_address
-- ----------------------------
BEGIN;
INSERT INTO `pre_member_address` VALUES (1, 1, '收货手机', '收货联系人', '收货人详细地址', '100010', 1, '上海', 2, '上海市', 2, '嘉定区', 3, 0, 0, 0, '2022-07-29 11:19:13', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (2, 1, '收货手机', '收货联系人', '收货人详细地址', '100010', 1, '上海', 2, '上海市', 2, '嘉定区', 3, 0, 0, 0, '2022-07-29 11:19:13', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (3, 1, '收货手机', '收货联系人', '收货人详细地址', '100010', 1, '上海', 2, '上海市', 2, '嘉定区', 3, 0, 0, 0, '2022-07-29 11:19:13', '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (4, 1, '收货手机', '收货联系人', '收货人详细地址', '100010', 1, '上海', 2, '上海市', 2, '嘉定区', 3, 0, 1, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_member_favorite
-- ----------------------------
DROP TABLE IF EXISTS `pre_member_favorite`;
CREATE TABLE `pre_member_favorite`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL DEFAULT 0 COMMENT '用户ID',
  `goods_id` int(11) NOT NULL DEFAULT 0 COMMENT '收藏商品ID',
  `status` mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单状态码 0:订单关闭 1订单创建 2:订单已支付  3:订单超卖(付款但是订单被关闭) ',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  `dateline` bigint(20) NOT NULL DEFAULT 0 COMMENT '收藏时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_uid`(`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '我的商品收藏';

-- ----------------------------
-- Table structure for pre_member_security
-- ----------------------------
DROP TABLE IF EXISTS `pre_member_security`;
CREATE TABLE `pre_member_security`  (
  `uid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户UID',
  `salt` char(24)  NOT NULL DEFAULT '' COMMENT '密钥',
  `secondary_password` char(255)  NOT NULL DEFAULT '' COMMENT '密码',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户安全表';

-- ----------------------------
-- Table structure for pre_notice
-- ----------------------------
DROP TABLE IF EXISTS `pre_notice`;
CREATE TABLE `pre_notice`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '推荐ID',
  `title` char(255)  NOT NULL DEFAULT '' COMMENT '标题',
  `title_pic` char(255)  NULL DEFAULT NULL COMMENT '标题图',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型 0 网址链接 1:NFT商品',
  `type_data` char(255)  NOT NULL DEFAULT '' COMMENT '类型关联数据(链接 或者 ID)',
  `start_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '开始时间 - 每日零时',
  `end_time` bigint(20) NOT NULL DEFAULT 0 COMMENT '结束时间- 当日某个时刻时间',
  `start_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间 - 每日零时',
  `end_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间- 当日某个时刻时间',
  `content` text  NOT NULL DEFAULT '' COMMENT '详情 html代码',
  `weight` mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_weight`(`weight`) USING BTREE,
  INDEX `idx_start_at`(`start_at`) USING BTREE,
  INDEX `idx_start_time`(`start_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '公告';

-- ----------------------------
-- Table structure for pre_order
-- ----------------------------
DROP TABLE IF EXISTS `pre_order`;
CREATE TABLE `pre_order`  (
  `order_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `order_sn` char(30)  NOT NULL DEFAULT '' COMMENT '订单号',
  `goods_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '购买商品',
  `goods_sn` char(30)  NOT NULL DEFAULT '' COMMENT '货号',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `nickname` char(24)  NOT NULL DEFAULT '' COMMENT '用户昵称',
  `goods_name` char(255)  NOT NULL DEFAULT '' COMMENT '购买商品名称',
  `goods_title_pic` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品标题图',
  `goods_thumb_pic` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `goods_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品封面图',
  `goods_buy_num` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品购买数量',
  `goods_price` decimal(20, 2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品单价',
  `goods_total_cost` decimal(20, 2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单总价',
  `payment` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否需要三方支付',
  `payment_type` varchar(255)  NOT NULL DEFAULT '' COMMENT '三方支付方式 wxpay:微信',
  `payment_cost` decimal(14, 2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '三方支付金额',
  `payment_datetime` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '三方支付时间',
  `payment_pay_transaction_id` char(100)  NOT NULL DEFAULT '' COMMENT '三方支付订单号',
  `payment_data` text  NOT NULL DEFAULT '' COMMENT '详情 ',
  `link_phone` varchar(32)  NOT NULL DEFAULT '' COMMENT '收货手机',
  `link_man` varchar(255)  NOT NULL DEFAULT '' COMMENT '收货联系人',
  `link_address` varchar(255)  NOT NULL DEFAULT '' COMMENT '收货地址',
  `province_id` int(11) NOT NULL DEFAULT 0 COMMENT '省ID',
  `province` varchar(100) NOT NULL DEFAULT '' COMMENT '省',
  `city_id` int(11) NOT NULL DEFAULT 0 COMMENT '市ID',
  `city` varchar(100) NOT NULL DEFAULT '' COMMENT '市',
  `area_id` int(11) NOT NULL DEFAULT 0 COMMENT '区县ID',
  `area` varchar(100) NOT NULL DEFAULT '' COMMENT '区县',
  `county_id` int(11) NOT NULL DEFAULT 0 COMMENT '国家ID',
  `address` varchar(500)  NOT NULL COMMENT '收货人详细地址',
  `contract_metadata_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'contract_metadata_id',
  `is_shiped` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'nft是否发放',
  `order_type`  mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '下单类型 0:用户下单 1:免费领取 2:后台发放  3:报名空投 4:兑换码兑换',
  `status` mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单状态码 0:订单关闭 1订单创建 2:订单已支付  3:订单超卖(付款但是订单被关闭) ',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`order_id`) USING BTREE,
  INDEX `idx_goods_sn`(`goods_sn`) USING BTREE,
  INDEX `idx_uid_status`(`uid`, `status`) USING BTREE,
  INDEX `idx_status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '订单库';

-- ----------------------------
-- Records of pre_order
-- ----------------------------
BEGIN;
INSERT INTO `pre_order`(`order_id`, `order_sn`, `goods_id`, `goods_sn`, `uid`, `nickname`, `goods_name`, `goods_title_pic`, `goods_thumb_pic`, `goods_image`, `goods_buy_num`, `goods_price`, `goods_total_cost`, `payment`, `payment_type`, `payment_cost`, `payment_datetime`, `payment_pay_transaction_id`, `payment_data`, `link_phone`, `link_man`, `link_address`, `province_id`, `city_id`, `county_id`, `province`, `city`, `area`, `address`, `is_shiped`, `status`, `updated_at`, `created_at`, `deleted_at`) VALUES (1, '202207281826009998640001', 1, 'E2525225522', 1, '星河探索者#1', '测试NFT', '', '', 'https://assets.starfission.cn/admin/static/JeyfRMitmA.jpg', 1, 50.00, 50.00, 0, '', 0.00, '0000-00-00 00:00:00', '', '', '收货手机', '收货联系人', '收货人详细地址', 1, 2, 3, '上海', '上海市', '嘉定区', '收货人详细地址', 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `pre_order`(`order_id`, `order_sn`, `goods_id`, `goods_sn`, `uid`, `nickname`, `goods_name`, `goods_title_pic`, `goods_thumb_pic`, `goods_image`, `goods_buy_num`, `goods_price`, `goods_total_cost`, `payment`, `payment_type`, `payment_cost`, `payment_datetime`, `payment_pay_transaction_id`, `payment_data`, `link_phone`, `link_man`, `link_address`, `province_id`, `city_id`, `county_id`, `province`, `city`, `area`, `address`, `is_shiped`, `status`, `updated_at`, `created_at`, `deleted_at`) VALUES (2, '202207281826053088640002', 1, 'E2525225522', 1, '星河探索者#1', '测试NFT', '', '', 'https://assets.starfission.cn/admin/static/JeyfRMitmA.jpg', 1, 50.00, 50.00, 0, '', 0.00, '0000-00-00 00:00:00', '', '', '收货手机', '收货联系人', '收货人详细地址', 1, 2, 3, '上海', '上海市', '嘉定区', '收货人详细地址', 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for pre_release
-- ----------------------------
DROP TABLE IF EXISTS `pre_release`;
CREATE TABLE `pre_release`  (
  `release_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '发行方ID',
  `release_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '发行方名称',
  `release_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '发行方图片',
  `release_full_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '发行方完整文字',
  `is_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `order` bigint(20) NOT NULL DEFAULT 0 COMMENT '排序',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`release_id`) USING BTREE,
  INDEX `idx_is_deleted`(`is_deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品发行方';

-- ----------------------------
-- Records of pre_release
-- ----------------------------
BEGIN;
INSERT INTO `pre_release` VALUES (1, '发行方测试1', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_test
-- ----------------------------
DROP TABLE IF EXISTS `pre_test`;
CREATE TABLE `pre_test`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '发布用户ID',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'test';

-- ----------------------------
-- Table structure for pre_wallet
-- ----------------------------
DROP TABLE IF EXISTS `pre_wallet`;
CREATE TABLE `pre_wallet`  (
  `uid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `eth_address` varchar(255)  NOT NULL DEFAULT '' COMMENT '以太坊地址',
  `cfx_main_address` varchar(255)  NOT NULL DEFAULT '' COMMENT '树图主网钱包地址',
  `cfx_test_address` varchar(255)  NOT NULL DEFAULT '' COMMENT '树图测试网钱包地址',
  `private_key` varchar(255)  NOT NULL DEFAULT '' COMMENT '私钥',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 101 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '钱包信息库';

-- ----------------------------
-- Records of pre_wallet
-- ----------------------------
BEGIN;
INSERT INTO `pre_wallet` VALUES (1, '0x642c1f9e9dcd31cdf6b2a10176d74f59802d6e73', 'cfx:aamc2h68x1gxdxt00muuc701k7p2annsspfjv87xyt', 'cfxtest:aamc2h68x1gxdxt00muuc701k7p2annssptdcs53uf', 'a6f8cfdbd238b909fca254e4827e0849d99fb7087e10b7622444eb34cd3932b3', '2022-07-27 13:36:13', '2022-07-27 13:36:13', '2022-07-27 13:36:13'), (3, '0x414ca32aa5aa45287ecafcb89c15ef6646cfa169', 'cfx:aajy3j3my0zemmd83n8nvhaz77xerx7brec0akapfs', 'cfxtest:aajy3j3my0zemmd83n8nvhaz77xerx7brej7x3ggbe', 'df8a4a7eacfdddf891b529bd4df28d9754a1972f34677e7fafe4cb3ac848ba34', '2022-07-27 13:52:56', '2022-07-27 13:52:56', '2022-07-27 13:52:56'), (7, '0x5740e84fffaa823c59614d51bf08359346c61a6e', 'cfx:aanyb4ct98zjetc3pfgzdt2jg0kyrvu4r25z6a4zkk', 'cfxtest:aanyb4ct98zjetc3pfgzdt2jg0kyrvu4r2v8tu69rd', '48ea699ab855be5f664bd49983015404fbf62dbe9f6f1859dd7558d4266d3682', '2022-07-27 15:24:32', '2022-07-27 15:24:32', '2022-07-27 15:24:32'), (100, '0x161e77eccd8f71f862023501e4938ab2132a08c5', 'cfx:aanb679p30h1d8dcaj4ud3exvm3bgmuj2yd5khad1y', 'cfxtest:aanb679p30h1d8dcaj4ud3exvm3bgmuj2yku41gkx8', 'df322f3dee2fcf28de586f9308c24f8a3b642458263750b7b099bf7606290c9f', '2022-07-25 16:28:00', '2022-07-25 16:28:00', '0000-00-00 00:00:00');
COMMIT;

-- ----------------------------
-- Table structure for pre_wallet_goods
-- ----------------------------
DROP TABLE IF EXISTS `pre_wallet_goods`;
CREATE TABLE `pre_wallet_goods`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',
  

  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `nickname` varchar(255)  NOT NULL DEFAULT '',
  `transaction_hash` varchar(256)  NOT NULL DEFAULT '' COMMENT '交易哈希',
  `contract_hash` varchar(256)  NOT NULL DEFAULT '' COMMENT '钱包合约地址',
  `wallet_hash` varchar(256)  NOT NULL DEFAULT '' COMMENT '拥有人钱包地址',

  `goods_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '购买商品',
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `goods_thumb_pic` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品缩略图',
   `goods_title_pic` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品标题图',
  `goods_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品封面图',
  `goods_ar` varchar(500)  NOT NULL DEFAULT '' COMMENT 'ar 模型',
  `goods_ar_image` varchar(500)  NOT NULL DEFAULT '' COMMENT 'ar加载图',
  `goods_tags` varchar(255)  NOT NULL DEFAULT '' COMMENT '标签 使用英文逗号间隔 ',

  `copyright_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '版权方ID',
  `copyright_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '版权方名字',
  `brand_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '品牌ID',
  `brand_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '品牌方名称',
  `release_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '发行方ID',
  `release_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '发行方名称',

  
  `blockchain_id` bigint(20) NOT NULL DEFAULT 0  COMMENT '区块链ID',
  `blockchain_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '区块链名字',
  `blockchain_key` varchar(255) NOT NULL DEFAULT '' COMMENT '区块链key',
  `blockchain_icon` varchar(255) NOT NULL DEFAULT '' COMMENT '区块链ICON',

  `contract_metadata_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '商品ID',
  `contract_metadata_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应的名字',
  `contract_metadata_description` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应的描述',
  `contract_metadata_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应的固定图片',
  `contract_metadata_animation_url` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应播放媒体地址',

  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_uid`(`uid`) USING BTREE,
  INDEX `idx_goods_id`(`goods_id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '钱包商品';

-- ----------------------------
-- Records of pre_wallet_goods
-- ----------------------------


-- ----------------------------
-- Table structure for pre_wallet_security
-- ----------------------------
DROP TABLE IF EXISTS `pre_wallet_security`;
CREATE TABLE `pre_wallet_security`  (
  `uid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户UID',
  `salt` char(24)  NOT NULL DEFAULT '' COMMENT '密钥',
  `secondary_password` char(255)  NOT NULL DEFAULT '' COMMENT '密码',
  `updated_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',

  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '钱包安全表';

DROP TABLE IF EXISTS `pre_transfer`;
CREATE TABLE `pre_transfer`  (

  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',

  `from_uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源用户ID',
  `from_nickname` varchar(255)  NOT NULL DEFAULT '' COMMENT '来源昵称',
  `from_wallet_hash` varchar(256)  NOT NULL DEFAULT '' COMMENT '来源钱包地址',
  `to_uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收人id',
  `to_nickname` varchar(255)  NOT NULL DEFAULT '' COMMENT '接收人昵称',
  `to_wallet_hash` varchar(256)  NOT NULL DEFAULT '' COMMENT '接收人钱包地址',

  `transaction_hash` varchar(256)  NOT NULL DEFAULT '' COMMENT '交易哈希',

  `goods_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '购买商品',
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `goods_thumb_pic` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `goods_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '商品封面图',
  `goods_ar` varchar(500)  NOT NULL DEFAULT '' COMMENT 'ar 模型',
  `goods_ar_image` varchar(500)  NOT NULL DEFAULT '' COMMENT 'ar加载图',
  `goods_tags` varchar(255)  NOT NULL DEFAULT '' COMMENT '标签 使用英文逗号间隔 ',

  `copyright_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '版权方ID',
  `copyright_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '版权方名字',
  `brand_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '品牌ID',
  `brand_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '品牌方名称',
  `release_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '发行方ID',
  `release_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '发行方名称',

  
  `blockchain_id` bigint(20) NOT NULL DEFAULT 0  COMMENT '区块链ID',
  `blockchain_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '区块链名字',
  `blockchain_key` varchar(255) NOT NULL DEFAULT '' COMMENT '区块链key',
  `blockchain_icon` varchar(255) NOT NULL DEFAULT '' COMMENT '区块链ICON',

  `contract_metadata_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '商品ID',
  `contract_metadata_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应的名字',
  `contract_metadata_description` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应的描述',
  `contract_metadata_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应的固定图片',
  `contract_metadata_animation_url` varchar(255)  NOT NULL DEFAULT '' COMMENT '合约metadata对应播放媒体地址',

  `type` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '下单类型 0:购买 1:赠出 2:获赠 3:空投 4:合成',

  `updated_at`  datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
    INDEX `idx_to_uid`(`to_uid`) USING BTREE,
  INDEX `idx_goods_id`(`goods_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 66 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '交易流转';


DROP TABLE IF EXISTS `pre_redeem_code`;
CREATE TABLE `pre_redeem_code`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `code` varchar(40) NOT NULL COMMENT '兑换码',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '绑定用户 如果为0 则不绑定',
  `auid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '后台用户ID',
  `goods_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '绑定商品',
  `type` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '下单类型 0:购买 1:赠出 2:获赠 3:空投 4:合成',
  `start_at`  datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始兑换时间',
  `end_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束兑换时间',
  `is_used` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'nft是否发放',
  `updated_at`  datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_code`(`code`) USING BTREE,
  INDEX `idx_uid`(`uid`) USING BTREE,
  INDEX `idx_goods_id`(`goods_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 66 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '兑换码';

ALTER TABLE `ly_order` 
ADD COLUMN   `payment_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '供前端暂存使用的 支付类型' ;



ALTER TABLE `pre_order` 
ADD COLUMN   `goods_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0：实物商品，1：数字藏品，2：实物礼包， 3：数字藏品-盲盒' after `goods_name`;


ALTER TABLE `pre_order` 
ADD COLUMN   `goods_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0：实物商品，1：数字藏品，2：实物礼包， 3：数字藏品-盲盒' after `goods_name`;



ALTER TABLE `pre_wallet_goods` 
ADD COLUMN   `contract_token_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '合约 token id' after `blockchain_icon`;


ALTER TABLE `pre_wallet_goods` 
ADD COLUMN  `contract_metadata_url` varchar(255) NOT NULL DEFAULT '' COMMENT  '合约metadata文件访问地址' after `blockchain_icon`;

ALTER TABLE `pre_transfer` 
ADD COLUMN  `contract_token_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '合约 token id' after `blockchain_icon`;



ALTER TABLE `pre_order` 
ADD COLUMN    `goods_url` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '连接关键词(英文)' after `goods_name`;



  


ALTER TABLE `pre_banner` 
ADD COLUMN      `short_title` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除' after `weight`;
ALTER TABLE `pre_notice` 
ADD COLUMN      `short_title` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除' after `weight`;

ALTER TABLE `pre_banner` 
ADD COLUMN      `short_title` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '短标题' after `weight`;
ALTER TABLE `pre_notice` 
ADD COLUMN      `short_title` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '短标题' after `weight`;



  

DROP TABLE IF EXISTS `pre_transfer_given`;
CREATE TABLE `pre_transfer_given`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源用户ID',
  `nickname` varchar(255)  NOT NULL DEFAULT '' COMMENT '来源昵称',
  `wallet_hash` varchar(256)  NOT NULL DEFAULT '' COMMENT '来源钱包地址',
  `to_uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收人id',
  `to_nickname` varchar(255)  NOT NULL DEFAULT '' COMMENT '接收人昵称',
  `to_wallet_hash` varchar(256)  NOT NULL DEFAULT '' COMMENT '接收人钱包地址',
  `wallet_goods_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源用户ID',
  `status` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '状态 0:待处理 1:处理完毕',
  `updated_at`  datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
    INDEX `idx_uid`(`uid`) USING BTREE,
  INDEX `idx_wallet_goods_id`(`wallet_goods_id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户转赠列表';



DROP TABLE IF EXISTS `pre_client_version`;
CREATE TABLE `pre_client_version`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `platform` mediumint(8) unsigned NOT NULL DEFAULT 1 COMMENT '平台 1:安卓 2:ios',
  `version_code` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '版本号',
  `version_name` varchar(255)  NOT NULL DEFAULT '' COMMENT '版本名',
  `details` text  NULL DEFAULT NULL COMMENT '版本更新详情',
  `resource_url` varchar(255)  NOT NULL DEFAULT '' COMMENT '版本更新资源地址',
  `updated_at`  datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_platform_version_code`(`platform`,`version_code`)
) ENGINE = InnoDB  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '客户端更新表';


SET FOREIGN_KEY_CHECKS = 1;


ALTER TABLE `pre_order` 
DROP COLUMN `is_charge_off`;

ALTER TABLE `pre_order` 
ADD COLUMN  `is_charge_off`  tinyint(1) unsigned NOT NULL DEFAULT 0  COMMENT '订单是否核销'  after `is_shiped`;


ALTER TABLE `pre_order` 
ADD COLUMN  `charge_off_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '核销时间'  after `is_charge_off`;




ALTER TABLE `pre_order` 
ADD COLUMN  `wallet_goods_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '我的藏品ID(钱包商品ID)'  after `contract_metadata_id`;






ALTER TABLE `pre_wallet_goods` 
ADD COLUMN `is_shiped` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'nft是否发放(是否铸造完成)'  after `contract_metadata_animation_url`;






DROP TABLE IF EXISTS `pre_wallet_goods_metadata_clog`;
CREATE TABLE `pre_wallet_goods_metadata_clog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `order_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户订单',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `wallet_goods_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '我的藏品ID(钱包商品ID)',
  `body` LONGTEXT  NOT NULL DEFAULT '' COMMENT '商品内容',
  `status` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '处理状态 0:尚未处理 1:已处理 ',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE,
  KEY `idx_order_id_status` (`order_id`,`status`) USING BTREE,
  KEY `idx_wallet_goods_id_status` (`wallet_goods_id`,`status`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单关联藏品更新记录';

 
DROP TABLE IF EXISTS `pre_redeem_code`;
CREATE TABLE `pre_redeem_code`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `code` varchar(40) NOT NULL COMMENT '兑换码',
  `batch_id`  varchar(40) NOT NULL  COMMENT '批次ID',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '绑定用户 如果为0 则不绑定',
  `auid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '后台用户ID',
  `goods_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '绑定商品',

  `remain_qty` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '剩余数量 ',
  `is_used` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否使用',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `used_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '使用最后时间',
  `start_at`  datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始兑换时间',
  `end_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束兑换时间',
  `created_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at`  datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime(0) NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_code`(`code`) USING BTREE,
  INDEX `batch_id`(`batch_id`) USING BTREE,
  INDEX `idx_uid`(`uid`) USING BTREE,
  INDEX `idx_goods_id`(`goods_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '兑换码';





DROP TABLE IF EXISTS `pre_contract_template`;
CREATE TABLE `pre_contract_template` (

  `contract_template_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '合约类型模板ID',
  `title`  varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '合约模板标题',
  `blockchain_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '区块链类型',
  `blockchain_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '区块链名字',
  `blockchain_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '区块链key',
  `blockchain_icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '区块链ICON',
  `blockchain_address` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '区块链地址',
  `contract_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '合约类型 ',
  `contract_network` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所在的网络(树图直接网络ID)',
  `contract_tokenuri_url_domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'tokenuri 访问地址域名',
  `contract_tokenuri_url_pre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'tokenuri 访问地址前缀',
  `contract_keystore_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'keystore地址',

  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`contract_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='合约模板';





ALTER TABLE `pre_goods` 
ADD COLUMN  `contract_template_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '合约类型模板ID'  before `blockchain_id`;


DROP TABLE IF EXISTS `pre_topic`;
CREATE TABLE `pre_topic` (
  `topic_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '专题ID',
  `category_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '所属分类ID',
  `topic_name` varchar(255)  NOT NULL COMMENT '商品名称',
  `topic_url` varchar(256)  NOT NULL DEFAULT '' COMMENT '连接关键词(英文)',
  `topic_image` varchar(255)  NOT NULL DEFAULT '' COMMENT '专题描述',
  `topic_desc` varchar(255)  NOT NULL DEFAULT '' COMMENT '专题描述',
  `is_hide` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否隐藏显示',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `weight` bigint(20) NOT NULL DEFAULT 0 COMMENT '排序权重',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`topic_id`) USING BTREE,
  KEY `idx_category_id` (`category_id`,`is_hide`,`weight`,`is_deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='专题';



DROP TABLE IF EXISTS `pre_topic_goods`;
CREATE TABLE `pre_topic_goods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `topic_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '专题ID',
  `goods_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '商品ID',
  `weight` bigint(20) NOT NULL DEFAULT 0 COMMENT '排序权重',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `idx_topic_id_goods_id` (`topic_id`,`goods_id`),
  KEY `idx_topic_id_weight` (`topic_id`,`weight`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='专题商品分类';



ALTER TABLE `pre_member` 
ADD COLUMN  `app_id` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '注册来源 应用的ID';


DROP TABLE IF EXISTS `pre_app`;
CREATE TABLE `pre_app` (
  `app_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '应用ID',
  `app_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应用名称',
  `app_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '专题商品页访问地址',
  `app_secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用于加密的密钥',
  `goods_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '上链商品ID',
  `notify_domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '异步通知安全域名',
  `is_published` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否已上线(已发布)',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`app_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='开放应用';


DROP TABLE IF EXISTS `pre_app_user`;
CREATE TABLE `pre_app_user` (
  `app_uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT  COMMENT '应用UID',
  `open_id` varchar(40)  NOT NULL COMMENT '用户openID',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '对应的用户id',
  `app_id` bigint(20) NOT NULL DEFAULT 0 COMMENT 'appID',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`app_uid`),
  UNIQUE KEY `idx_open_id` (`open_id`),
  UNIQUE KEY `idx_open_id_uid` (`open_id`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='应用用户信息';
-- return_url 返回地址

DROP TABLE IF EXISTS `pre_app_order`;
CREATE TABLE `pre_app_order` (
  `app_order_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT  COMMENT 'APP 订单ID',
  `order_id`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT'订单ID',
  `app_uid`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT'应用UID',
  `open_id` varchar(40)  NOT NULL COMMENT '用户openID',
  `goods_id`  bigint(20) UNSIGNED NOT NULL DEFAULT 0  COMMENT '商品ID',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '对应的用户id',
  `app_id` bigint(20) NOT NULL DEFAULT 0 COMMENT 'appID',
  `out_trade_no` varchar(255)  NOT NULL COMMENT '商户订单',
  `metadata_image` varchar(255)  NOT NULL COMMENT '图像元数据',
  `notify_url` varchar(255)  NOT NULL COMMENT '通知URL',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`app_order_id`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='应用订单信息';



ALTER TABLE `pre_app_order` 
ADD COLUMN  `is_success_notifyed` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否已经成功通知';



ALTER TABLE `pre_goods_category` 
ADD COLUMN  `category_url` varchar(255)  NOT NULL COMMENT '分类URL'  AFTER `category_name_en`;



ALTER TABLE `pre_topic` 
ADD COLUMN  `redirect_uri` varchar(255)  NOT NULL COMMENT '跳转地址'  AFTER `topic_desc`;



ALTER TABLE `pre_goods` 
ADD COLUMN  `topic_ids` varchar(255)  NOT NULL COMMENT '跳转地址'  AFTER `category_id`;


DROP TABLE IF EXISTS `pre_recommend`;
CREATE TABLE `pre_recommend` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '推荐ID',
  `display_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '显示类型   1:banner只显示在某一个分类下 2:banner显示在所有列表内(包括全部商品列表,专题商品列表)',
  `category_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '所属分类ID,如果为0 则显示在',
  `title` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `title_short` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '短标题',
  `title_pic` char(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '标题图',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '介绍 非html 代码',
 
  `item_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '类型 0 网址链接 1:goods 商品ID 2:topic 专题ID 3: category分类ID',
  `item_type_data` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型关联数据(链接 或者 ID)',
  
  `start_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间 - 每日零时',
  `end_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间- 当日某个时刻时间',
  `weight` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '排序',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_weight` (`display_type`,`category_id`,`weight`) USING BTREE,
  KEY `idx_start_at` (`start_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='推荐';


ALTER TABLE `pre_topic` 
ADD COLUMN   `topic_banner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '专题Banner'  AFTER `topic_image`;




DROP TABLE IF EXISTS `pre_hannels`;
CREATE TABLE `pre_hannels` (
  `hannels_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '渠道ID',
  `hannels_title` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '渠道名字',
  `hannels_key` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '渠道关键字',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '标记删除时间',
  PRIMARY KEY (`hannels_id`) USING BTREE,
 UNIQUE KEY `idx_hannels_key` (`hannels_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='渠道';


ALTER TABLE `pre_member` 
ADD COLUMN   `hannels_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '渠道ID'  AFTER `change_name_num`;


ALTER TABLE  `pre_member` 
ADD INDEX `idx_hannels_id`(`hannels_id`);


ALTER TABLE `pre_order` 
ADD COLUMN   `hannels_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '渠道ID'  AFTER `app_order_id`;


ALTER TABLE  `pre_order` 
ADD INDEX `idx_hannels_id`(`hannels_id`);



ALTER TABLE `pre_order` 
ADD COLUMN   `express_number` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '快递单号'  AFTER `status`;
ALTER TABLE `pre_order` 
ADD COLUMN   `express_company` char(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '快递公司'  AFTER `status`;
