ALTER TABLE `ayx_donater`
ADD COLUMN `location`  int(11) NULL COMMENT '地点' AFTER `uptime`;
ALTER TABLE `ayx_family`
ADD COLUMN `location`  int(11) NULL COMMENT '地点' AFTER `uptime`;
ALTER TABLE `ayx_good`
ADD COLUMN `location`  int(11) NULL COMMENT '地点' AFTER `uptime`;
ALTER TABLE `ayx_record`
ADD COLUMN `location`  int(11) NULL COMMENT '地点' AFTER `uptime`;
ALTER TABLE `ayx_user`
ADD COLUMN `location`  int(11) NULL COMMENT '地点' AFTER `lastloginip`;
CREATE TABLE `ayx_location` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`name`  varchar(50)  NULL COMMENT '地点名' ,
`comment`  text  NULL COMMENT '有关地点的注释' ,
PRIMARY KEY (`id`)
) ENGINE=MyISAM;
;
