-- CREATE TABLE `ohrm_leave` (
--   `id` int(11) NOT NULL,
--   `leave_date` date default NULL,
--   `leave_length_hours` decimal(6,2) unsigned default NULL,
--   `leave_length_days` decimal(4,2) unsigned default NULL,
--   `leave_status` smallint(6) default NULL,
--   `leave_comments` varchar(256) default NULL,
--   `leave_request_id` int(11) NOT NULL,
--   `leave_type_id` int(11) NOT NULL,
--   `employee_id` int(7) NOT NULL,
--   `start_time` time default NULL,
--   `end_time` time default NULL,
--   PRIMARY KEY  (`id`,`leave_request_id`,`leave_type_id`,`employee_id`),
--   KEY `leave_request_id` (`leave_request_id`,`leave_type_id`,`employee_id`),
--   KEY `leave_type_id` (`leave_type_id`),
--   KEY `employee_id` (`employee_id`),
--   KEY `type_status` (`leave_request_id`,`leave_status`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELETE FROM ohrm_leave_entitlement;
DELETE FROM ohrm_leave_type;
DELETE FROM ohrm_leave;

alter table `hs_hr_leavetype` add column int_id int not null auto_increment unique key;

INSERT INTO `ohrm_leave_type` (`id`, `name`, `deleted`, `operational_country_id`)
                    SELECT old_lt.`int_id`, old_lt.`leave_type_name`, 
                    IF(old_lt.`available_flag` = 1, 0, 1) , old_lt.`operational_country_id`
                    FROM `hs_hr_leavetype` old_lt;

INSERT INTO `ohrm_leave_entitlement`(emp_number, no_of_days, leave_type_id, from_date, to_date, 
                                credited_date, note, entitlement_type, `deleted`)
            SELECT q.employee_id, q.no_of_days_allotted, lt.int_id, p.leave_period_start_date, p.leave_period_end_date, 
            p.leave_period_start_date, 'record created by upgrade', 1, 0
            FROM `hs_hr_employee_leave_quota` q LEFT JOIN `hs_hr_leavetype` lt ON lt.leave_type_id = q.leave_type_id
            LEFT JOIN hs_hr_leave_period p ON p.leave_period_id = q.leave_period_id; 

-- INSERT INTO `ohrm_leave` (`id`, `leave_date`, `leave_length_hours`, `leave_length_days`,
--                   `leave_status`, `leave_comments`, `leave_request_id`,
--                   `leave_type_id`, `employee_id`, `start_time`, `end_time`)
--                    SELECT old_l.`leave_id`, old_l.`leave_date`, old_l.`leave_length_hours`, old_l.`leave_length_days`,
--                           old_l.`leave_status`, old_l.`leave_comments`, old_l.`leave_request_id`,
--                           lt.int_id, old_l.`employee_id`, 
--                           old_l.`start_time`, old_l.`end_time`
--                     FROM `hs_hr_leave` old_l LEFT JOIN `hs_hr_leavetype` lt ON old_l.leave_type_id = lt.leave_type_id;

alter table `hs_hr_leavetype` drop column int_id;
