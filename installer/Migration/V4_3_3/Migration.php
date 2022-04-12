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
        /*$this->getSchemaHelper()->changeColumn('hs_hr_employee', 'emp_number', [
            'Type' => Type::getType(Types::INTEGER), 'Length' => 7, 'Notnull' => true, 'Autoincrement' => true
        ]);*/
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet', 'timesheet_id', [
            'Type' => Type::getType(Types::BIGINT), 'Length' => 20, 'Notnull' => true, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet_item', 'timesheet_item_id', [
            'Type' => Type::getType(Types::BIGINT), 'Length' => 20, 'Notnull' => true, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet_action_log', 'timesheet_action_log_id', [
            'Type' => Type::getType(Types::BIGINT), 'Length' => 20, 'Notnull' => true, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_attendance_record', 'id', [
            'Type' => Type::getType(Types::BIGINT), 'Length' => 20, 'Notnull' => true, 'Autoincrement' => true
        ]);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_attachment', ['ohrm_job_candidate_attachment_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_vacancy', ['ohrm_job_candidate_vacancy_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview', ['ohrm_job_interview_ibfk_2']);

        $this->getSchemaHelper()->changeColumn('ohrm_job_candidate', 'id', [
            'Type' => Type::getType(Types::INTEGER), 'Length' => 13, 'Notnull' => true, 'Autoincrement' => true
        ]);

        $jobCandidateAttachmentFK = new ForeignKeyConstraint(
            ['candidate_id'],
            'ohrm_job_candidate',
            ['id'],
            'ohrm_job_candidate_attachment_ibfk_1',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_attachment', $jobCandidateAttachmentFK);

        $jobCandidateHistoryFK1 = new ForeignKeyConstraint(
            ['candidate_id'],
            'ohrm_job_candidate',
            ['id'],
            'ohrm_job_candidate_history_ibfk_1',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_history', $jobCandidateHistoryFK1);

        $jobCandidateVacancyFK1 = new ForeignKeyConstraint(
            ['candidate_id'],
            'ohrm_job_candidate',
            ['id'],
            'ohrm_job_candidate_vacancy_ibfk_1',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_vacancy', $jobCandidateVacancyFK1);

        $jobInterviewFK2 = new ForeignKeyConstraint(
            ['candidate_id'],
            'ohrm_job_candidate',
            ['id'],
            'ohrm_job_interview_ibfk_2',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_interview', $jobInterviewFK2);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview', ['ohrm_job_interview_ibfk_1']);

        $this->getSchemaHelper()->changeColumn('ohrm_job_candidate_vacancy', 'id', [
            'Type' => Type::getType(Types::INTEGER), 'Length' => 13, 'Notnull' => true, 'Autoincrement' => true
        ]);

        $jobInterviewFK1 = new ForeignKeyConstraint(
            ['candidate_vacancy_id'],
            'ohrm_job_candidate_vacancy',
            ['id'],
            'ohrm_job_interview_ibfk_1',
            ['onDelete' => 'SET NULL']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_interview', $jobInterviewFK1);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_2']);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_vacancy', ['ohrm_job_candidate_vacancy_ibfk_2']);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_vacancy_attachment', ['ohrm_job_vacancy_attachment_ibfk_1']);

        $this->getSchemaHelper()->changeColumn('ohrm_job_vacancy', 'id', [
            'Type' => Type::getType(Types::INTEGER), 'Length' => 13, 'Notnull' => true, 'Autoincrement' => true
        ]);

        $jobCandidateHistoryFK2 = new ForeignKeyConstraint(
            ['vacancy_id'],
            'ohrm_job_vacancy',
            ['id'],
            'ohrm_job_candidate_history_ibfk_2',
            ['onDelete' => 'SET NULL']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_history', $jobCandidateHistoryFK2);

        $jobCandidateVacancyFK2 = new ForeignKeyConstraint(
            ['vacancy_id'],
            'ohrm_job_vacancy',
            ['id'],
            'ohrm_job_candidate_vacancy_ibfk_2',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_vacancy', $jobCandidateVacancyFK2);

        $jobVacancyAttachmentFK1 = new ForeignKeyConstraint(
            ['vacancy_id'],
            'ohrm_job_vacancy',
            ['id'],
            'ohrm_job_vacancy_attachment_ibfk_1',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_vacancy_attachment', $jobVacancyAttachmentFK1);
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.3.3';
    }
}
