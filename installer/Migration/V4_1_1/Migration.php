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

namespace OrangeHRM\Installer\Migration\V4_1_1;

use Doctrine\DBAL\Schema\Index;
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
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_member_detail', ['hs_hr_emp_member_detail_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_member_detail', ['hs_hr_emp_member_detail_ibfk_2']);

        $this->getSchemaHelper()->dropPrimaryKey('hs_hr_emp_member_detail');

        $this->getSchemaHelper()->addColumn('hs_hr_emp_member_detail', 'id', Types::INTEGER, ['Length' => 6, 'Notnull' => true, 'Default' => null]);

        $primaryKey = new Index(
            null,
            ['id'],
            true,
            true
        );
        $this->getSchemaHelper()->getSchemaManager()->createIndex($primaryKey, 'hs_hr_emp_member_detail');

        $this->getSchemaHelper()->changeColumn('hs_hr_emp_member_detail', 'id', [
            'Notnull' => true, 'Default' => null, 'Autoincrement' => true
        ]);
        $this->getSchemaHelper()->enableConstraints();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.1.1';
    }
}
