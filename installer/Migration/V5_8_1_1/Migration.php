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

namespace OrangeHRM\Installer\Migration\V5_8_1_1;

use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        // Increase email field lengths to 120 characters (where currently less than 120)
        
        // 1. Employee work email (50 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE hs_hr_employee 
             MODIFY COLUMN emp_work_email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
        );
        
        // 2. Employee other email (50 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE hs_hr_employee 
             MODIFY COLUMN emp_oth_email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
        );
        
        // 3. Candidate email (100 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_candidate 
             MODIFY COLUMN email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL'
        );
        
        // 4. Organization email (30 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_organization_gen_info 
             MODIFY COLUMN email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
        );
        
        // 5. Reset password email (60 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_reset_password_request 
             MODIFY COLUMN reset_email VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL'
        );
        
        // Note: ohrm_email_subscriber.email stays at 255 (no change)
        // Note: ohrm_email_configuration.sent_as stays at 250 (no change)
        
        // Increase username field length to 120 characters
        
        // 6. User username (40 -> 120)
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_user 
             MODIFY COLUMN user_name VARCHAR(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
        );
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.8.1.1';
    }
}
