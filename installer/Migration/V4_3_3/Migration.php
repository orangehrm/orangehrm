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
        $this->getSchemaHelper()->disableConstraints();

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
        $this->getSchemaHelper()->enableConstraints();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.3.3';
    }
}
