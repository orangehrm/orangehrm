<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Installer\Migration\V4_3_3;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->removeConflictingForeignKeys();

        $this->getSchemaHelper()->changeColumn('hs_hr_employee', 'emp_number', [
            'Type' => Type::getType(Types::INTEGER), 'Length' => 7, 'Notnull' => true, 'Default' => null,'Autoincrement' => true
        ]);

        $this->getSchemaHelper()->changeColumn('ohrm_timesheet', 'timesheet_id', [
            'Type' => Type::getType(Types::BIGINT), 'Length' => 20, 'Notnull' => true, 'Default' => null, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet_item', 'timesheet_item_id', [
            'Type' => Type::getType(Types::BIGINT), 'Length' => 20, 'Notnull' => true, 'Default' => null, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet_action_log', 'timesheet_action_log_id', [
            'Type' => Type::getType(Types::BIGINT), 'Length' => 20, 'Notnull' => true, 'Default' => null, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_attendance_record', 'id', [
            'Type' => Type::getType(Types::BIGINT), 'Length' => 20, 'Notnull' => true, 'Default' => null, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_job_candidate', 'id', [
            'Type' => Type::getType(Types::INTEGER), 'Length' => 13, 'Notnull' => true, 'Default' => null, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_job_candidate_vacancy', 'id', [
            'Type' => Type::getType(Types::INTEGER), 'Length' => 13, 'Notnull' => true, 'Default' => null, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_job_vacancy', 'id', [
            'Type' => Type::getType(Types::INTEGER), 'Length' => 13, 'Notnull' => true, 'Default' => null, 'Autoincrement' => true
        ]);

        $this->recreateRemovedForeignKeys();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.3.3';
    }

    private function removeConflictingForeignKeys(): void
    {
        // hs_hr_employee
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_history_of_ealier_pos', ['hs_hr_emp_history_of_ealier_pos_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_reportto', ['hs_hr_emp_reportto_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_reportto', ['hs_hr_emp_reportto_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_vacancy', ['ohrm_job_vacancy_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_employee_work_shift', ['ohrm_employee_work_shift_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_emergency_contacts', ['hs_hr_emp_emergency_contacts_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_4']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_performance_tracker_log', ['ohrm_performance_tracker_log_fk2']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_work_experience', ['hs_hr_emp_work_experience_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_performance_review', ['ohrm_performance_review_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_language', ['hs_hr_emp_language_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_project_admin', ['ohrm_project_admin_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_basicsalary', ['hs_hr_emp_basicsalary_ibfk_3']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_leave_entitlement', ['ohrm_leave_entitlement_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_passport', ['hs_hr_emp_passport_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_emp_termination', ['ohrm_emp_termination_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_us_tax', ['hs_hr_emp_us_tax_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_leave_adjustment', ['ohrm_leave_adjustment_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_attachment', ['hs_hr_emp_attachment_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_locations', ['hs_hr_emp_locations_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_contract_extend', ['hs_hr_emp_contract_extend_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_skill', ['hs_hr_emp_skill_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_dependents', ['hs_hr_emp_dependents_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_leave_comment', ['ohrm_leave_comment_ibfk_3']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview_interviewer', ['ohrm_job_interview_interviewer_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_leave_request_comment', ['ohrm_leave_request_comment_ibfk_3']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_leave_request', ['ohrm_leave_request_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_emp_education', ['ohrm_emp_education_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_performance_tracker_reviewer', ['ohrm_performance_tracker_reviewer_fk2']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate', ['ohrm_job_candidate_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_emp_license', ['ohrm_emp_license_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_user', ['ohrm_user_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_children', ['hs_hr_emp_children_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_picture', ['hs_hr_emp_picture_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_performance_track', ['ohrm_performance_track_fk1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_performance_track', ['ohrm_performance_track_fk2']);

        // ohrm_job_candidate
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview', ['ohrm_job_interview_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_vacancy', ['ohrm_job_candidate_vacancy_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_attachment', ['ohrm_job_candidate_attachment_ibfk_1']);

        // ohrm_job_candidate_vacancy
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview', ['ohrm_job_interview_ibfk_1']);

        // ohrm_job_vacancy
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_vacancy', ['ohrm_job_candidate_vacancy_ibfk_2']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_vacancy_attachment', ['ohrm_job_vacancy_attachment_ibfk_1']);
    }

    private function recreateRemovedForeignKeys(): void
    {
        //alter table hs_hr_emp_history_of_ealier_pos
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_history_of_ealier_pos', ['hs_hr_emp_history_of_ealier_pos_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_history_of_ealier_pos', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_history_of_ealier_pos_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_reportto
        //       add constraint foreign key (erep_sup_emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_reportto', ['hs_hr_emp_reportto_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_reportto', ['erep_sup_emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_reportto_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_reportto
        //       add constraint foreign key (erep_sub_emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_reportto', ['hs_hr_emp_reportto_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_reportto', ['erep_sub_emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_reportto_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table ohrm_job_vacancy
        //       add constraint foreign key (hiring_manager_id)
        //                             references hs_hr_employee(emp_number) on delete set null;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_vacancy', ['ohrm_job_vacancy_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_vacancy', ['hiring_manager_id'], 'hs_hr_employee', ['emp_number'], 'ohrm_job_vacancy_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table `ohrm_employee_work_shift`
        //  add constraint foreign key (`emp_number`) references `hs_hr_employee` (`emp_number`) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_employee_work_shift', ['ohrm_employee_work_shift_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_employee_work_shift', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_employee_work_shift_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_emergency_contacts
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_emergency_contacts', ['hs_hr_emp_emergency_contacts_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_emergency_contacts', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_emergency_contacts_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table ohrm_job_candidate_history
        //       add constraint foreign key (performed_by)
        //                             references hs_hr_employee(emp_number) on delete set null;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_4']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_candidate_history', ['performed_by'], 'hs_hr_employee', ['emp_number'], 'ohrm_job_candidate_history_ibfk_4', ['onDelete' => 'SET NULL']);

        //CONSTRAINT `ohrm_performance_tracker_log_fk2` FOREIGN KEY (`reviewer_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_performance_tracker_log', ['ohrm_performance_tracker_log_fk2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_performance_tracker_log', ['reviewer_id'], 'hs_hr_employee', ['emp_number'], 'ohrm_performance_tracker_log_fk2', ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE']);

        //alter table hs_hr_emp_work_experience
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_work_experience', ['hs_hr_emp_work_experience_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_work_experience', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_work_experience_ibfk_1', ['onDelete' => 'CASCADE']);

        //ALTER TABLE `ohrm_performance_review`
        //  ADD CONSTRAINT FOREIGN KEY (`employee_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_performance_review', ['ohrm_performance_review_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_performance_review', ['employee_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_performance_review_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_language
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_language', ['hs_hr_emp_language_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_language', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_language_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table `ohrm_project_admin`
        //  add constraint foreign key (`emp_number`) references `hs_hr_employee` (`emp_number`) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_project_admin', ['ohrm_project_admin_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_project_admin', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_project_admin_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_basicsalary
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_basicsalary', ['hs_hr_emp_basicsalary_ibfk_3']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_basicsalary', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_basicsalary_ibfk_3', ['onDelete' => 'CASCADE']);

        //alter table ohrm_leave_entitlement
        //    add foreign key (emp_number)
        //        references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_leave_entitlement', ['ohrm_leave_entitlement_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_leave_entitlement', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_leave_entitlement_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_passport
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_passport', ['hs_hr_emp_passport_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_passport', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_passport_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table ohrm_emp_termination
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_emp_termination', ['ohrm_emp_termination_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_emp_termination', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_emp_termination_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_us_tax
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_us_tax', ['hs_hr_emp_us_tax_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_us_tax', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_us_tax_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table ohrm_leave_adjustment
        //    add foreign key (emp_number)
        //        references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_leave_adjustment', ['ohrm_leave_adjustment_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_leave_adjustment', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_leave_adjustment_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_attachment
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_attachment', ['hs_hr_emp_attachment_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_attachment', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_attachment_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table `hs_hr_emp_locations`
        //    add constraint foreign key (`emp_number`)
        //        references hs_hr_employee(`emp_number`) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_locations', ['hs_hr_emp_locations_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_locations', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_locations_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_contract_extend
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_contract_extend', ['hs_hr_emp_contract_extend_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_contract_extend', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_contract_extend_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_skill
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_skill', ['hs_hr_emp_skill_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_skill', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_skill_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_dependents
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_dependents', ['hs_hr_emp_dependents_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_dependents', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_dependents_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table ohrm_leave_comment
        //    add constraint foreign key (created_by_emp_number)
        //        references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_leave_comment', ['ohrm_leave_comment_ibfk_3']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_leave_comment', ['created_by_emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_leave_comment_ibfk_3', ['onDelete' => 'CASCADE']);

        //alter table ohrm_job_interview_interviewer
        //       add constraint foreign key (interviewer_id)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview_interviewer', ['ohrm_job_interview_interviewer_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_interview_interviewer', ['interviewer_id'], 'hs_hr_employee', ['emp_number'], 'ohrm_job_interview_interviewer_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table ohrm_leave_request_comment
        //    add constraint foreign key (created_by_emp_number)
        //        references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_leave_request_comment', ['ohrm_leave_request_comment_ibfk_3']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_leave_request_comment', ['created_by_emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_leave_request_comment_ibfk_3', ['onDelete' => 'CASCADE']);

        //alter table ohrm_leave_request
        //    add constraint foreign key (emp_number)
        //        references hs_hr_employee (emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_leave_request', ['ohrm_leave_request_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_leave_request', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_leave_request_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table ohrm_emp_education
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_emp_education', ['ohrm_emp_education_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_emp_education', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_emp_education_ibfk_1', ['onDelete' => 'CASCADE']);

        //CONSTRAINT `ohrm_performance_tracker_reviewer_fk2` FOREIGN KEY (`reviewer_id`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_performance_tracker_reviewer', ['ohrm_performance_tracker_reviewer_fk2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_performance_tracker_reviewer', ['reviewer_id'], 'hs_hr_employee', ['emp_number'], 'ohrm_performance_tracker_reviewer_fk2', ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE']);

        //alter table ohrm_job_candidate
        //       add constraint foreign key (added_person)
        //                             references hs_hr_employee(emp_number) on delete set null;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate', ['ohrm_job_candidate_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_candidate', ['added_person'], 'hs_hr_employee', ['emp_number'], 'ohrm_job_candidate_ibfk_1', ['onDelete' => 'SET NULL']);

        //alter table ohrm_emp_license
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_emp_license', ['ohrm_emp_license_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_emp_license', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_emp_license_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table `ohrm_user`
        //    add constraint foreign key (`emp_number`)
        //        references hs_hr_employee(`emp_number`) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_user', ['ohrm_user_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_user', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_user_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_children
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_children', ['hs_hr_emp_children_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_children', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_children_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table hs_hr_emp_picture
        //       add constraint foreign key (emp_number)
        //                             references hs_hr_employee(emp_number) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_picture', ['hs_hr_emp_picture_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('hs_hr_emp_picture', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'hs_hr_emp_picture_ibfk_1', ['onDelete' => 'CASCADE']);

        //CONSTRAINT `ohrm_performance_track_fk1` FOREIGN KEY (`emp_number`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE,
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_performance_track', ['ohrm_performance_track_fk1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_performance_track', ['emp_number'], 'hs_hr_employee', ['emp_number'], 'ohrm_performance_track_fk1', ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE']);

        //CONSTRAINT `ohrm_performance_track_fk2` FOREIGN KEY (`added_by`) REFERENCES `hs_hr_employee` (`emp_number`) ON DELETE CASCADE ON UPDATE CASCADE
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_performance_track', ['ohrm_performance_track_fk2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_performance_track', ['added_by'], 'hs_hr_employee', ['emp_number'], 'ohrm_performance_track_fk2', ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE']);

        //alter table ohrm_job_interview
        //       add constraint foreign key (candidate_id)
        //                             references ohrm_job_candidate(id) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview', ['ohrm_job_interview_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_interview', ['candidate_id'], 'ohrm_job_candidate', ['id'], 'ohrm_job_interview_ibfk_2', ['onDelete' => 'CASCADE']);

        //alter table ohrm_job_candidate_history
        //       add constraint foreign key (candidate_id)
        //                             references ohrm_job_candidate(id) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_candidate_history', ['candidate_id'], 'ohrm_job_candidate', ['id'], 'ohrm_job_candidate_history_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table ohrm_job_candidate_vacancy
        //       add constraint foreign key (candidate_id)
        //                             references ohrm_job_candidate(id) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_vacancy', ['ohrm_job_candidate_vacancy_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_candidate_vacancy', ['candidate_id'], 'ohrm_job_candidate', ['id'], 'ohrm_job_candidate_vacancy_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table ohrm_job_candidate_attachment
        //       add constraint foreign key (candidate_id)
        //                             references ohrm_job_candidate(id) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_attachment', ['ohrm_job_candidate_attachment_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_candidate_attachment', ['candidate_id'], 'ohrm_job_candidate', ['id'], 'ohrm_job_candidate_attachment_ibfk_1', ['onDelete' => 'CASCADE']);

        //alter table ohrm_job_interview
        //       add constraint foreign key (candidate_vacancy_id)
        //                             references ohrm_job_candidate_vacancy(id) on delete set null;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview', ['ohrm_job_interview_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_interview', ['candidate_vacancy_id'], 'ohrm_job_candidate_vacancy', ['id'], 'ohrm_job_interview_ibfk_1', ['onDelete' => 'SET NULL']);

        //alter table ohrm_job_candidate_history
        //       add constraint foreign key (vacancy_id)
        //                             references ohrm_job_vacancy(id) on delete set null;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_candidate_history', ['vacancy_id'], 'ohrm_job_vacancy', ['id'], 'ohrm_job_candidate_history_ibfk_2', ['onDelete' => 'SET NULL']);

        //alter table ohrm_job_candidate_vacancy
        //       add constraint foreign key (vacancy_id)
        //                             references ohrm_job_vacancy(id) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_vacancy', ['ohrm_job_candidate_vacancy_ibfk_2']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_candidate_vacancy', ['vacancy_id'], 'ohrm_job_vacancy', ['id'], 'ohrm_job_candidate_vacancy_ibfk_2', ['onDelete' => 'CASCADE']);


        //alter table ohrm_job_vacancy_attachment
        //       add constraint foreign key (vacancy_id)
        //                             references ohrm_job_vacancy(id) on delete cascade;
        //$this->getSchemaHelper()->dropForeignKeys('ohrm_job_vacancy_attachment', ['ohrm_job_vacancy_attachment_ibfk_1']);
        $this->createForeignKeyConstraintAndAddToTable('ohrm_job_vacancy_attachment', ['vacancy_id'], 'ohrm_job_vacancy', ['id'], 'ohrm_job_vacancy_attachment_ibfk_1', ['onDelete' => 'CASCADE']);

    }

    /**
     * @param string $tableName
     * @param array $columnNames
     * @param string $foreignTableName
     * @param array $foreignColumnNames
     * @param string $constraintName
     * @param array $constraintOptions
     */
    private function createForeignKeyConstraintAndAddToTable(
        string $tableName,
        array $columnNames,
        string $foreignTableName,
        array $foreignColumnNames,
        string $constraintName,
        array $constraintOptions
    ): void
    {
        $foreignKeyConstraint = new ForeignKeyConstraint(
            $columnNames,
            $foreignTableName,
            $foreignColumnNames,
            $constraintName,
            $constraintOptions
        );

        $this->getSchemaHelper()->addForeignKey($tableName, $foreignKeyConstraint);
    }
}
