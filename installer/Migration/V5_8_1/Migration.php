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

namespace OrangeHRM\Installer\Migration\V5_8_1;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

/**
 * Widen columns that store Cryptographer output: AES-256-GCM ciphertext is longer than legacy AES-ECB hex.
 */
class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $string512 = [
            'Type' => Type::getType(Types::STRING),
            'Length' => 512,
            'Notnull' => false,
        ];

        $this->getSchemaHelper()->changeColumn('hs_hr_emp_basicsalary', 'ebsal_basic_salary', $string512);
        $this->getSchemaHelper()->changeColumn('hs_hr_employee', 'emp_ssn_num', $string512);
        $this->getSchemaHelper()->changeColumn('ohrm_email_configuration', 'smtp_password', $string512);
        $this->getSchemaHelper()->changeColumn('ohrm_auth_provider_extra_details', 'client_secret', $string512);
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.8.1';
    }
}
