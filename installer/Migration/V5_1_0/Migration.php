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

namespace OrangeHRM\Installer\Migration\V5_1_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    public function up(): void
    {
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->addValidColumnToRequestResetPassword();
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_kpi CHANGE job_title_code job_title_code INT(13) NOT NULL'
        );
        $kpiForeignKeyConstraint = new ForeignKeyConstraint(
            ['job_title_code'],
            'ohrm_job_title',
            ['id'],
            'ohrm_kpi_for_job_title_id',
            ['onCascade' => 'DELETE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_kpi', $kpiForeignKeyConstraint);
    }

    /**
     * @return void
     */
    private function addValidColumnToRequestResetPassword(): void
    {
        $this->getSchemaHelper()->addColumn(
            'ohrm_reset_password',
            'expired',
            Types::BOOLEAN,
            ['Default' => true, 'Notnull' => true]
        );
    }

    public function getVersion(): string
    {
        return '5.1.0';
    }
}
