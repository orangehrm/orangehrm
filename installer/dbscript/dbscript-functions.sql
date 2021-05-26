DROP FUNCTION IF EXISTS dashboard_get_subunit_parent_id;

DELIMITER $$

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

			END; $$
			
CREATE EVENT leave_taken_status_change
	ON SCHEDULE EVERY 1 HOUR STARTS NOW()
	DO
	  BEGIN
		UPDATE hs_hr_leave SET leave_status = 3 WHERE leave_status = 2 AND leave_date < DATE(NOW());
	  END;$$
                        
DELIMITER ;                        