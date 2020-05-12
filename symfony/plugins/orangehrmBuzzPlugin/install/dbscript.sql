
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES ('buzz_refresh_time','60000'),
        ('buzz_share_count','10'),
        ('buzz_initial_comments','2'),
        ('buzz_viewmore_comment','5'),
        ('buzz_like_count','5'),
        ('buzz_time_format','h:i a'),
        ('buzz_most_like_posts','5'),
        ('buzz_post_text_lenth','500'),
        ('buzz_post_text_lines','5'),
        ('buzz_cookie_valid_time','5000'),
        ('buzz_most_like_shares','5'),
        ('buzz_image_max_dimension', '1024');
--
-- Inserting News feed Module to The Database
--
INSERT INTO `ohrm_module`( `name`, `status`) VALUES ('buzz','1');

set @admin_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'Admin');
set @ESS_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'ESS');
set @Supervisor_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'Supervisor');

INSERT INTO `ohrm_data_group` (`name`, `description`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES
('buzz_link', 'buzz link permition ', 1, 1, 1, 0);

SET @buzz_link_data_group_id := (SELECT id FROM `ohrm_data_group` WHERE `name` = 'buzz_link');

INSERT INTO `ohrm_user_role_data_group` (`user_role_id`, `data_group_id`, `can_read`, `can_create`, `can_update`, `can_delete`, `self`) VALUES
(@ESS_role_id, @buzz_link_data_group_id, 1, 1, 1, 0, 0),
(@Supervisor_role_id, @buzz_link_data_group_id, 1, 1, 1, 0, 0);

INSERT INTO `ohrm_data_group` (`name`, `description`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES
('buzz_link_admin', 'buzz link permission for admin', 1, 1, 1, 0);

SET @buzz_link_admin_data_group_id := (SELECT id FROM `ohrm_data_group` WHERE `name` = 'buzz_link_admin');

INSERT INTO `ohrm_user_role_data_group` (`user_role_id`, `data_group_id`, `can_read`, `can_create`, `can_update`, `can_delete`, `self`) VALUES
(@admin_role_id, @buzz_link_admin_data_group_id, 1, 1, 1, 0, 0);

--
-- Table structure for table `ohrm_buzz_post`
--
CREATE TABLE IF NOT EXISTS `ohrm_buzz_post` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_number` int(7) ,
  `text` text ,
  `post_time` datetime NOT NULL,
  `updated_at` timestamp ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employee_number` (`employee_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
--
-- Constraints for table `ohrm_buzz_post`
--
ALTER TABLE `ohrm_buzz_post`
  ADD CONSTRAINT `buzzPostEmployee` FOREIGN KEY (`employee_number`) 
    REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Table structure for table `ohrm_buzz_share`
--
CREATE TABLE IF NOT EXISTS `ohrm_buzz_share` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `employee_number` int(7) ,
  `number_of_likes` int(6) DEFAULT NULL,
  `number_of_unlikes` int(6) DEFAULT NULL,
  `number_of_comments` int(6) DEFAULT NULL,
  `share_time` datetime NOT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `text` text,
  `updated_at` timestamp ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `employee_number` (`employee_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
--
-- Constraints for table `ohrm_buzz_share`
--
ALTER TABLE `ohrm_buzz_share`
  ADD CONSTRAINT `buzzShareEmployee` FOREIGN KEY (`employee_number`) 
    REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `buzzSharePost` FOREIGN KEY (`post_id`) 
    REFERENCES `ohrm_buzz_post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Table structure for table `ohrm_buzz_comment`
--
CREATE TABLE IF NOT EXISTS `ohrm_buzz_comment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `share_id` bigint(20) NOT NULL,
  `employee_number` int(7) ,
  `number_of_likes` int(6) DEFAULT NULL,
  `number_of_unlikes` int(6) DEFAULT NULL,
  `comment_text` text,
  `comment_time` datetime NOT NULL,
  `updated_at` timestamp ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
  PRIMARY KEY (`id`),
  KEY `share_id` (`share_id`),
  KEY `employee_number` (`employee_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
--
-- Constraints for table `ohrm_buzz_comment`
--
ALTER TABLE `ohrm_buzz_comment`
  ADD CONSTRAINT `buzzComentedEmployee` FOREIGN KEY (`employee_number`) 
    REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `buzzComentOnShare` FOREIGN KEY (`share_id`) 
    REFERENCES `ohrm_buzz_share` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Table structure for table `ohrm_buzz_like_on_comment`
--
CREATE TABLE IF NOT EXISTS `ohrm_buzz_like_on_comment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) NOT NULL,
  `employee_number` int(7) ,
  `like_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_id` (`comment_id`),
  KEY `employee_number` (`employee_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
--
-- Constraints for table `ohrm_buzz_comment_like`
--
ALTER TABLE `ohrm_buzz_like_on_comment`
  ADD CONSTRAINT `buzzCommentLikeEmployee` FOREIGN KEY (`employee_number`) 
    REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `buzzLikeOnComment` FOREIGN KEY (`comment_id`) 
    REFERENCES `ohrm_buzz_comment` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Table structure for table `ohrm_buzz_share_like`
--
CREATE TABLE IF NOT EXISTS `ohrm_buzz_like_on_share` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `share_id` bigint(20) NOT NULL,
  `employee_number` int(7) ,
  `like_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `share_id` (`share_id`),
  KEY `employee_number` (`employee_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
--
-- Constraints for table `ohrm_buzz_share_like`
--
ALTER TABLE `ohrm_buzz_like_on_share`
  ADD CONSTRAINT `buzzShareLikeEmployee` FOREIGN KEY (`employee_number`) 
    REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `buzzLikeOnshare` FOREIGN KEY (`share_id`) 
    REFERENCES `ohrm_buzz_share` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Table structure for table `ohrm_buzz_photo`
--
CREATE TABLE IF NOT EXISTS `ohrm_buzz_photo` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `photo` mediumblob,
  `filename` varchar(100) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `width` varchar(20) DEFAULT NULL,
  `height` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attachment_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
--
-- Constraints for table `ohrm_buzz_photo`
--
ALTER TABLE `ohrm_buzz_photo`
  ADD CONSTRAINT `photoAttached` FOREIGN KEY (`post_id`) 
    REFERENCES `ohrm_buzz_post` (`id`) ON DELETE CASCADE;

--
-- Table structure for table `ohrm_buzz_link`
--

CREATE TABLE IF NOT EXISTS `ohrm_buzz_link` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `link` text NOT NULL,
  `type` tinyint(2) DEFAULT NULL,
  `title` VARCHAR( 600 ) NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `attachment_id` (`post_id`),
  KEY `photo_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
--
-- Constraints for table `ohrm_buzz_link`
--
ALTER TABLE `ohrm_buzz_link`
  ADD CONSTRAINT `linkAttached` FOREIGN KEY (`post_id`) 
    REFERENCES `ohrm_buzz_post` (`id`) ON DELETE CASCADE;

--
-- Table structure for table `ohrm_buzz_unlike_on_comment`
--
CREATE TABLE IF NOT EXISTS `ohrm_buzz_unlike_on_comment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) NOT NULL,
  `employee_number` int(7) ,
  `like_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_id` (`comment_id`),
  KEY `employee_number` (`employee_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
--
-- Constraints for table `ohrm_buzz_comment_like`
--
ALTER TABLE `ohrm_buzz_unlike_on_comment`
  ADD CONSTRAINT `buzzCommentUnLikeEmployee` FOREIGN KEY (`employee_number`) 
    REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `buzzUnLikeOnComment` FOREIGN KEY (`comment_id`) 
    REFERENCES `ohrm_buzz_comment` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Table structure for table `ohrm_buzz_share_like`
--
CREATE TABLE IF NOT EXISTS `ohrm_buzz_unlike_on_share` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `share_id` bigint(20) NOT NULL,
  `employee_number` int(7) ,
  `like_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `share_id` (`share_id`),
  KEY `employee_number` (`employee_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
--
-- Constraints for table `ohrm_buzz_share_like`
--
ALTER TABLE `ohrm_buzz_unlike_on_share`
  ADD CONSTRAINT `buzzShareUnLikeEmployee` FOREIGN KEY (`employee_number`) 
    REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `buzzUNLikeOnshare` FOREIGN KEY (`share_id`) 
    REFERENCES `ohrm_buzz_share` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

INSERT INTO `hs_hr_config`(`key`, `value`) VALUES ('buzz_comment_text_lenth','250');

-- Add Buzz As A menu Item
SET @buzz_module_id = (SELECT `id` FROM `ohrm_module` WHERE `name`='buzz');
INSERT INTO `ohrm_screen`(`name`, `module_id`, `action_url`) VALUES ('Buzz',@buzz_module_id,'viewBuzz');
SET @screen_id=(SELECT `id` FROM `ohrm_screen` WHERE `name`='Buzz');
INSERT INTO `ohrm_menu_item`(`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `status`) VALUES ('Buzz', @screen_id, NULL, '1', '1500', 1);

INSERT INTO `ohrm_user_role_screen`(`user_role_id`, `screen_id`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES (@admin_role_id,@screen_id,1,1,1,1);
INSERT INTO `ohrm_user_role_screen`(`user_role_id`, `screen_id`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES (@ESS_role_id,@screen_id,1,1,1,1);
INSERT INTO `ohrm_user_role_screen`(`user_role_id`, `screen_id`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES (@Supervisor_role_id,@screen_id,1,1,1,1);

CREATE TABLE IF NOT EXISTS `ohrm_buzz_notification_metadata` (
  `emp_number` int(7) ,
  `last_notification_view_time` datetime DEFAULT NULL,
  `last_buzz_view_time` datetime DEFAULT NULL,
  `last_clear_notifications` datetime DEFAULT NULL,
  PRIMARY KEY (`emp_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

ALTER TABLE `ohrm_buzz_notification_metadata`
  ADD CONSTRAINT `notificationMetadata` FOREIGN KEY (`emp_number`)
    REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- i.e. -4 weeks, -2 days, -1 day, -1 month
-- https://www.php.net/manual/en/datetime.formats.relative.php
INSERT INTO `hs_hr_config`(`key`, `value`) VALUES ('buzz_max_notification_period','-1 week');
