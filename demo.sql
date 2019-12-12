
CREATE TABLE `session`(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `sessionid` varchar(255) NOT NULL COMMENT 'session_id',
  `sessionkey` varchar(255) NOT NULL COMMENT 'session_name',
  `sessionvalue` varchar(255) NOT NULL COMMENT 'sessionvalue',
  `expire` datetime DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='session';

