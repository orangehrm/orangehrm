CREATE TABLE IF NOT EXISTS `ohrm_openid_provider` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `provider_name` varchar(40) DEFAULT NULL,  
  `provider_url` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ohrm_auth_provider_extra_details` (
    `id` INT PRIMARY KEY AUTO_INCREMENT, 
    `provider_id` INT(10) NOT NULL,
    `provider_type` INT, 
    `client_id` TEXT, 
    `client_secret` TEXT, 
    `developer_key` TEXT, 
    CONSTRAINT FOREIGN KEY (`provider_id`) REFERENCES `ohrm_openid_provider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

CREATE TABLE IF NOT EXISTS `ohrm_openid_user_identity` (
  `user_id` int(10) ,
  `provider_id` int(10) ,
  `user_identity` varchar(255) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

ALTER TABLE `ohrm_openid_user_identity`
  ADD CONSTRAINT `ohrm_user_identity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ohrm_user` (`id`) ON DELETE SET NULL;
ALTER TABLE `ohrm_openid_user_identity`
  ADD CONSTRAINT `ohrm_user_identity_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `ohrm_openid_provider` (`id`) ON DELETE SET NULL;



-- ALTER TABLE `ohrm_openid_user_identity`
-- ADD CONSTRAINT `ohrm_user_identity_pk_1` PRIMARY KEY (`user_id`,`provider_id`)
INSERT INTO `hs_hr_config` (`key` ,`value`) VALUES ('domain.name',  'localhost');

INSERT INTO ohrm_screen ( `name`, `module_id`, `action_url`) VALUES ( 'Manage OpenId', 2, 'openIdProvider');
SET @opnid_screen_id := (SELECT LAST_INSERT_ID());

SET @admin_menu_id := (SELECT `id` FROM ohrm_menu_item WHERE `menu_title` = 'Admin' AND `level` = 1);
SET @configuration_id := (SELECT `id` FROM ohrm_menu_item WHERE `menu_title` = 'Configuration' AND `level` = 2 AND parent_id = @admin_menu_id);
SET @max_order := (SELECT MAX(`order_hint`) FROM ohrm_menu_item WHERE parent_id = @configuration_id);

INSERT INTO ohrm_menu_item ( `menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES 
('Social Media Authentication', @opnid_screen_id, @configuration_id, 3, @max_order+100, NULL, 1);

INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES  
(1, @opnid_screen_id, 1, 1, 1, 0);

INSERT INTO hs_hr_config (`key`, `value`) VALUES('openId.provider.added', 'on');