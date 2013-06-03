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

在视图里面增加了good的location

ALTER
ALGORITHM=UNDEFINED
DEFINER=`root`@`localhost`
SQL SECURITY DEFINER
VIEW `ayx_fullgood` AS
SELECT
ayx_good.id AS id,
ayx_good.serial AS goodserial,
ayx_good.`name` AS goodname,
ayx_good.step AS step,
ayx_good.number AS number,
ayx_good.unit AS unit,
ayx_good.measure AS measure,
ayx_good.receipt AS receipt,
ayx_good.checkoutman AS checkoutman,
ayx_good.checkouttime AS checkouttime,
ayx_good.donateday AS donatetime,
ayx_donater.serial AS donaterserial,
ayx_donater.`name` AS donatername,
ayx_donater.phone AS donaterphone,
ayx_donater.sex AS donatersex,
ayx_record.distributeday AS distributeday,
ayx_record.serial AS recordserial,
ayx_family.serial AS familyserial,
ayx_family.agent AS agent,
ayx_family.sex AS agentsex,
ayx_family.address AS address,
ayx_family.idcard AS idcard,
ayx_family.phone AS familyphone,
ayx_family.member AS member,
ayx_record.handman AS handman,
ayx_record.verifier AS verifier,
ayx_good.location AS location
from (((`ayx_good` left join `ayx_donater` on((`ayx_good`.`donater_id` = `ayx_donater`.`id`))) left join `ayx_record` on((`ayx_good`.`id` = `ayx_record`.`good_id`))) left join `ayx_family` on((`ayx_record`.`family_id` = `ayx_family`.`id`)))
where (`ayx_good`.`status` = 1) ;