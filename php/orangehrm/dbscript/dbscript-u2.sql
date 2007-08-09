INSERT INTO `hs_hr_empstat`
  (`estat_code`, `estat_name`)
  VALUES ('EST000', 'Terminated')
  ON DUPLICATE KEY UPDATE `estat_name`='Terminated';

INSERT INTO `hs_hr_jobtit_empstat`
  (`jobtit_code`, `estat_code`)
  SELECT `jobtit_code`, 'EST000' FROM `hs_hr_job_title`
  ON DUPLICATE KEY UPDATE `estat_code`='EST000';

INSERT IGNORE INTO `hs_hr_db_version` VALUES ('DVR001','mysql4.1','initial DB','2005-10-10 00:00:00','2005-12-20 00:00:00',null,null);
INSERT IGNORE INTO `hs_hr_file_version` VALUES ('FVR001',NULL,'Release 1','2006-03-15 00:00:00','2006-03-15 00:00:00',null,null,'file_ver_01');
INSERT IGNORE INTO `hs_hr_versions` VALUES ('VER001','Release 1','2006-03-15 00:00:00','2006-03-15 00:00:00',null,null,0,'DVR001','FVR001','version 1.0');

DELETE FROM `hs_hr_module` WHERE mod_id = 'MOD003';

INSERT IGNORE INTO `hs_hr_weekends` VALUES (1, 0);
INSERT IGNORE INTO `hs_hr_weekends` VALUES (2, 0);
INSERT IGNORE INTO `hs_hr_weekends` VALUES (3, 0);
INSERT IGNORE INTO `hs_hr_weekends` VALUES (4, 0);
INSERT IGNORE INTO `hs_hr_weekends` VALUES (5, 0);
INSERT IGNORE INTO `hs_hr_weekends` VALUES (6, 8);
INSERT IGNORE INTO `hs_hr_weekends` VALUES (7, 8);

INSERT IGNORE INTO `hs_hr_timesheet_submission_period` VALUES (1, 'week', 7, 1, 1, 7, 'Weekly');
