SET @admin_user_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'Admin' LIMIT 1);
SET @ess_user_role_id := (SELECT id FROM ohrm_user_role WHERE name = 'ESS' LIMIT 1);

SET @admin_home_page := (SELECT id FROM ohrm_home_page WHERE user_role_id = @admin_user_role_id LIMIT 1);
SET @ess_home_page := (SELECT id FROM ohrm_home_page WHERE user_role_id = @ess_user_role_id LIMIT 1);

UPDATE ohrm_home_page SET action = 'dashboard/index', priority = '15' WHERE user_role_id = @admin_home_page;
UPDATE ohrm_home_page SET action = 'dashboard/index', priority = '5' WHERE user_role_id = @ess_home_page;

INSERT INTO ohrm_module (name, status) VALUES ('dashboard', 1);
SET @dashboard_module := (SELECT id FROM ohrm_module WHERE name = 'dashboard' LIMIT 1);

INSERT INTO ohrm_screen (name, module_id, action_url) VALUES ('Dashboard', @dashboard_module, 'index');
SET @dashboard_screen := (SELECT id FROM ohrm_screen WHERE name = 'Dashboard' LIMIT 1);

INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
('Dashboard', @dashboard_screen, NULL, 1, 800, NULL, 1);

INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_user_role_id, @dashboard_screen, 1, 0, 0, 0),
(@ess_user_role_id, @dashboard_screen, 1, 0, 0, 0);

DELIMITER $$
DROP FUNCTION IF EXISTS dashboard_get_subunit_parent_id;$$

CREATE FUNCTION  dashboard_get_subunit_parent_id
(
  id INT
)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
SELECT (SELECT t2.id 
               FROM ohrm_subunit t2 
               WHERE t2.lft < t1.lft AND t2.rgt > t1.rgt    
               ORDER BY t2.rgt-t1.rgt ASC LIMIT 1) INTO @parent
FROM ohrm_subunit t1 WHERE t1.id = id;

RETURN @parent;

END;$$

-- grant execute on function dashboard.dashboard_get_subunit_parent_id  to 'dashboard'@'localhost';

DELIMITER ;