DROP TABLE IF EXISTS `box_zcbox_user`;
CREATE TABLE IF NOT EXISTS `box_zcbox_user` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`name` varchar(20) comment '用户名称（不是微信昵称）',
    `openid` varchar(50) NOT NULL comment '用户openid(微信,支付宝等其他平台)',
    `company` varchar(30) comment '用户所在公司',
    `role` int(2) NOT NULL DEFAULT 0 COMMENT '用户角色',
    `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '用户注册时间',
    PRIMARY KEY (`id`),
    KEY `openid` (`openid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `box_zcbox_tip`;
CREATE TABLE IF NOT EXISTS `box_zcbox_tip` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`uid` int(10) unsigned NOT NULL COMMENT '用户id',
	`title` text NOT NULL comment '提出问题',
	`content` text NOT NULL comment '意见内容',
	`note` text NOT NULL comment '联系方式',
	`reply` text NOT NULL comment '回复',
	`status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '意见状态',
    `rewarded` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '奖励状态',
    `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '意见创建时间',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
