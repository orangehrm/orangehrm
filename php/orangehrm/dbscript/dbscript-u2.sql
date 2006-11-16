alter table hs_hr_compstructtree
       add constraint foreign key (loc_code)
                             references hs_hr_location(loc_code) on delete restrict;

alter table hs_pr_salary_currency_detail
       add constraint foreign key (currency_id)
                             references hs_hr_currency_type(currency_id) on delete cascade;

alter table hs_pr_salary_currency_detail
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete cascade;

alter table hs_hr_location
       add constraint foreign key (loc_country)
                             references hs_hr_country(cou_code) on delete cascade;

alter table hs_hr_job_title
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete set null;

alter table hs_hr_jobtit_empstat
       add constraint foreign key (jobtit_code)
                             references hs_hr_job_title(jobtit_code) on delete cascade;

alter table hs_hr_jobtit_empstat
       add constraint foreign key (estat_code)
                             references hs_hr_empstat(estat_code) on delete cascade;

alter table hs_hr_membership
       add constraint foreign key (membtype_code)
                             references hs_hr_membership_type(membtype_code) on delete cascade;

alter table hs_hr_employee
       add constraint foreign key (work_station)
                             references hs_hr_compstructtree(id) on delete set NULL;

alter table hs_hr_employee
       add constraint foreign key (ethnic_race_code)
                             references hs_hr_ethnic_race(ethnic_race_code) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (nation_code)
                             references hs_hr_nationality(nat_code) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (job_title_code)
                             references hs_hr_job_title(jobtit_code) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (emp_status)
                             references hs_hr_empstat(estat_code) on delete set null;

alter table hs_hr_employee
       add constraint foreign key (eeo_cat_code)
                             references hs_hr_eec(eec_code) on delete set null;

alter table hs_hr_emp_children
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
                             
alter table hs_hr_emp_dependents
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
                             
alter table hs_hr_emp_emergency_contacts
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_history_of_ealier_pos
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_licenses
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
                             
alter table hs_hr_emp_licenses
       add constraint foreign key (licenses_code)
                             references hs_hr_licenses(licenses_code) on delete cascade;
                             
alter table hs_hr_emp_skill
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_skill
       add constraint foreign key (skill_code)
                             references hs_hr_skill(skill_code) on delete cascade;

alter table hs_hr_emp_attachment
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
                             
alter table hs_hr_emp_picture
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_education
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_education
       add constraint foreign key (edu_code)
                             references hs_hr_education(edu_code) on delete cascade;
                             
alter table hs_hr_emp_work_experience
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;


alter table hs_hr_emp_passport
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_member_detail
       add constraint foreign key (membtype_code)
                             references hs_hr_membership_type(membtype_code) on delete cascade;

alter table hs_hr_emp_member_detail
       add constraint foreign key (membship_code)
                             references hs_hr_membership(membship_code) on delete cascade;

alter table hs_hr_emp_member_detail
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_reportto
       add constraint foreign key (erep_sup_emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_reportto
       add constraint foreign key (erep_sub_emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;
                             
alter table hs_hr_emp_basicsalary
       add constraint foreign key (sal_grd_code)
                             references hs_pr_salary_grade(sal_grd_code) on delete cascade;

alter table hs_hr_emp_basicsalary
       add constraint foreign key (currency_id)
                             references hs_hr_currency_type(currency_id) on delete cascade;

alter table hs_hr_emp_basicsalary
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_language
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_emp_language
       add constraint foreign key (lang_code)
                             references hs_hr_language(lang_code) on delete cascade;

alter table hs_hr_emp_contract_extend
       add constraint foreign key (emp_number)
                             references hs_hr_employee(emp_number) on delete cascade;

alter table hs_hr_db_version
       add constraint foreign key (entered_by) 
       						references hs_hr_users (id) on delete cascade;

alter table hs_hr_db_version
       add constraint foreign key (modified_by) 
       						references hs_hr_users (id) on delete cascade;

alter table hs_hr_file_version
       add constraint foreign key (altered_module)
							references hs_hr_module (mod_id) on delete cascade;
       
alter table hs_hr_file_version
       add constraint foreign key (entered_by) 
       						references hs_hr_users (id) on delete cascade;
       						
alter table hs_hr_file_version
       add constraint foreign key (modified_by) 
       						references hs_hr_users (id) on delete cascade;

alter table hs_hr_module
       add constraint foreign key (version) 
       						references hs_hr_versions (id) on delete cascade;

alter table hs_hr_rights
       add constraint foreign key (mod_id) 
       						references hs_hr_module (mod_id) on delete cascade;
       						
alter table hs_hr_rights
       add constraint foreign key (userg_id) 
       						references hs_hr_user_group (userg_id) on delete cascade;

alter table hs_hr_users
       add constraint foreign key (modified_user_id)
       						references hs_hr_users (id) on delete cascade;
       						
alter table hs_hr_users
       add constraint foreign key (created_by)
       						references hs_hr_users (id) on delete cascade;
       
alter table hs_hr_users
       add constraint foreign key (userg_id) 
       						references hs_hr_user_group (userg_id) on delete set null;
       						
alter table hs_hr_users
       add constraint foreign key (emp_number) 
       						references hs_hr_employee (emp_number) on delete restrict;
       						
alter table hs_hr_versions
       add constraint foreign key (modified_by) 
       						references hs_hr_users (id) on delete cascade;
       						
alter table hs_hr_versions
       add constraint foreign key (created_by) 
       						references hs_hr_users (id) on delete cascade;
       						
alter table hs_hr_versions
       add constraint foreign key (db_version) 
       						references hs_hr_db_version (id) on delete cascade;
       						
alter table hs_hr_versions
       add constraint foreign key (file_version) 
       						references hs_hr_file_version (id) on delete cascade;

alter table hs_hr_emprep_usergroup
       add constraint foreign key (userg_id) 
       						references hs_hr_user_group (userg_id) on delete cascade;

alter table hs_hr_emprep_usergroup
       add constraint foreign key (rep_code) 
       						references hs_hr_empreport (rep_code) on delete cascade;
       						
alter table hs_hr_employee_leave_quota
       add constraint foreign key (leave_type_id) 
       						references hs_hr_leavetype (leave_type_id) on delete cascade;
       						
alter table hs_hr_employee_leave_quota
       add constraint foreign key (employee_id) 
       						references hs_hr_employee (emp_number) on delete cascade;
       						
alter table hs_hr_leave
       add constraint foreign key (employee_id) 
       						references hs_hr_employee (emp_number) on delete cascade;
							
alter table hs_hr_leave
       add constraint foreign key (leave_type_id) 
       						references hs_hr_leavetype (leave_type_id) on delete cascade;