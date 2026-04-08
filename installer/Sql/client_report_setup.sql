START TRANSACTION;
 
-- 1. Ensure screen exists
INSERT INTO ohrm_screen (name, module_id, action_url)
SELECT 'View Client Report', 5, 'displayClientReportCriteria'
WHERE NOT EXISTS (
    SELECT 1 FROM ohrm_screen 
    WHERE action_url = 'displayClientReportCriteria'
);
 
-- 2. Get the screen_id (assuming action_url is unique)
SET @screen_id = (
    SELECT id FROM ohrm_screen 
    WHERE action_url = 'displayClientReportCriteria'
    LIMIT 1
);
 
-- 3. Ensure menu item exists
INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, status)
SELECT 'Client Report', @screen_id, 61, 3, 250, 1
WHERE NOT EXISTS (
    SELECT 1 FROM ohrm_menu_item 
    WHERE menu_title = 'Client Report' AND parent_id = 61
);
 
-- 4. Ensure user_role_screen entry exists
INSERT INTO ohrm_user_role_screen (user_role_id, screen_id, can_read)
SELECT 1, @screen_id, 1
WHERE NOT EXISTS (
    SELECT 1 FROM ohrm_user_role_screen 
    WHERE user_role_id = 1 AND screen_id = @screen_id
);
 
-- 5. Update permissions safely (only if row exists)
UPDATE ohrm_user_role_screen
SET can_read = 1,
    can_create = 1,
    can_update = 1,
    can_delete = 1
WHERE user_role_id = 1 
  AND screen_id = @screen_id;
 
-- Commit changes
COMMIT;
 
 