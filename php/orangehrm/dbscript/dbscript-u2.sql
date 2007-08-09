INSERT INTO `hs_hr_empstat`
  (`estat_code`, `estat_name`)
  VALUES ('EST000', 'Terminated')
  ON DUPLICATE KEY UPDATE `estat_name`='Terminated';

INSERT INTO `hs_hr_jobtit_empstat`
  (`jobtit_code`, `estat_code`)
  SELECT `jobtit_code`, 'EST000' FROM `hs_hr_job_title`
  ON DUPLICATE KEY UPDATE `estat_code`='EST000';

DELETE FROM `hs_hr_module` WHERE mod_id = 'MOD003';
INSERT IGNORE INTO `hs_hr_module` VALUES ('MOD001','Admin','Koshika','koshika@beyondm.net','VER001','HR Admin');
INSERT IGNORE INTO `hs_hr_module` VALUES ('MOD002','PIM','Koshika','koshika@beyondm.net','VER001','HR Functions');
INSERT IGNORE INTO `hs_hr_module` VALUES ('MOD004','Report','Koshika','koshika@beyondm.net','VER001','Reporting');
INSERT IGNORE INTO `hs_hr_module` VALUES ('MOD005', 'Leave', 'Mohanjith', 'mohanjith@beyondm.net', 'VER001', 'Leave Tracking');
INSERT IGNORE INTO `hs_hr_module` VALUES ('MOD006', 'Time', 'Mohanjith', 'mohanjith@orangehrm.com', 'VER001', 'Time Tracking');
