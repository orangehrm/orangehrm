<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Installer\Migration\V4_3_3;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    public const CONFLICTING_FOREIGN_KEY_TABLES = [
        'hs_hr_emp_history_of_ealier_pos',
        'hs_hr_emp_reportto',
        'ohrm_job_vacancy',
        'ohrm_employee_work_shift',
        'hs_hr_emp_emergency_contacts',
        'ohrm_job_candidate_history',
        'ohrm_performance_tracker_log',
        'hs_hr_emp_work_experience',
        'ohrm_performance_review',
        'hs_hr_emp_language',
        'ohrm_project_admin',
        'hs_hr_emp_basicsalary',
        'ohrm_leave_entitlement',
        'hs_hr_emp_passport',
        'ohrm_emp_termination',
        'hs_hr_emp_us_tax',
        'ohrm_leave_adjustment',
        'hs_hr_emp_attachment',
        'hs_hr_emp_locations',
        'hs_hr_emp_contract_extend',
        'hs_hr_emp_skill',
        'hs_hr_emp_dependents',
        'ohrm_leave_comment',
        'ohrm_job_interview_interviewer',
        'ohrm_leave_request_comment',
        'ohrm_leave_request',
        'ohrm_emp_education',
        'ohrm_performance_tracker_reviewer',
        'ohrm_job_candidate',
        'ohrm_emp_license',
        'ohrm_user',
        'hs_hr_emp_children',
        'hs_hr_emp_picture',
        'ohrm_performance_track',
        'ohrm_job_interview',
        'ohrm_job_candidate_vacancy',
        'ohrm_job_candidate_attachment',
        'ohrm_job_vacancy_attachment',
        'hs_hr_emp_member_detail'
    ];

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $conflictingConstraints =  $this->getConflictingForeignKeys();

        $this->removeConflictingForeignKeys($conflictingConstraints);

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

        $this->recreateRemovedForeignKeys($conflictingConstraints);
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.3.3';
    }

    /**
     * @return array
     */
    private function getConflictingForeignKeys(): array
    {
        $foreignKeyArray = [];
        foreach (self::CONFLICTING_FOREIGN_KEY_TABLES as $table) {
            $tableDetails = $this->getSchemaManager()->listTableDetails($table);
            $foreignKeys = $tableDetails->getForeignKeys();
            foreach ($foreignKeys as $constraintName => $constraint) {
                if (in_array($constraint->getForeignTableName(), ['hs_hr_employee', 'ohrm_job_candidate', 'ohrm_job_candidate_vacancy', 'ohrm_job_vacancy'])) {
                    $foreignKeyArray[$constraintName] = ['constraint' => $constraint, 'localTable' => $table];
                }
            }
        }
        return $foreignKeyArray;
    }

    /**
     * @param array $conflictingConstraints
     */
    private function removeConflictingForeignKeys(array $conflictingConstraints): void
    {
        foreach ($conflictingConstraints as $constraintName => $conflictingConstraint) {
            $this->getSchemaHelper()->dropForeignKeys($conflictingConstraint['localTable'], [$constraintName]);
        }
    }

    /**
     * @param array $conflictingConstraints
     */
    private function recreateRemovedForeignKeys(array $conflictingConstraints): void
    {
        foreach ($conflictingConstraints as $conflictingConstraint) {
            $this->getSchemaHelper()->addForeignKey($conflictingConstraint['localTable'], $conflictingConstraint['constraint']);
        }
    }
}
