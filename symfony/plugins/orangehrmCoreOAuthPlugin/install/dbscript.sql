INSERT INTO `ohrm_module` (`name`, `status`) VALUES ('oauth', 1);

SET @module_id := (SELECT `id` FROM `ohrm_module` WHERE `name` = 'admin');

INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES
('OAuth', @module_id, 'registerOAuthClient');

SET @oauth_screen_id := (SELECT LAST_INSERT_ID());

SET @admin_screen_id := (SELECT `id` FROM ohrm_menu_item WHERE `menu_title` = 'Admin' AND `level` = 1); 

SET @authentication_menu_id := (SELECT id FROM ohrm_menu_item where menu_title = 'Authentication');

INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES
('OAuth', @oauth_screen_id, @authentication_menu_id, 3, 1000, '', 0);

SET @admin_role_id := (SELECT `id` FROM ohrm_user_role WHERE `name` = 'Admin');

INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_role_id, @oauth_screen_id, 1, 1, 1, 1);

CREATE TABLE ohrm_oauth_client ( client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80) NOT NULL, redirect_uri VARCHAR(2000)  NOT NULL, CONSTRAINT client_id_pk PRIMARY KEY (client_id));
CREATE TABLE ohrm_oauth_access_token (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL,scope VARCHAR(2000), CONSTRAINT access_token_pk PRIMARY KEY (access_token));
CREATE TABLE ohrm_oauth_authorization_code (authorization_code VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), redirect_uri VARCHAR(2000) NOT NULL, expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT auth_code_pk PRIMARY KEY (authorization_code));
CREATE TABLE ohrm_oauth_refresh_token ( refresh_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT refresh_token_pk PRIMARY KEY (refresh_token));
CREATE TABLE ohrm_oauth_user (username VARCHAR(255) NOT NULL, password VARCHAR(2000), first_name VARCHAR(255), last_name VARCHAR(255), CONSTRAINT username_pk PRIMARY KEY (username));
