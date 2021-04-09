DROP TABLE IF EXISTS `ohrm_reset_password`;

INSERT INTO `hs_hr_config` (`key`, `value`) VALUES
('authentication.status', 'Enable'),
('authentication.enforce_password_strength', 'on'),
('authentication.default_required_password_strength', 'medium');

CREATE  TABLE `ohrm_reset_password` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `reset_email` VARCHAR(60) NOT NULL,
  `reset_request_date` TIMESTAMP NOT NULL ,
  `reset_code` VARCHAR(200) NOT NULL ,
  PRIMARY KEY(`id`))
ENGINE = InnoDB DEFAULT CHARSET=UTF8;