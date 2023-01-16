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

namespace OrangeHRM\Installer\Migration\V5_4_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        if (!$this->getSchemaHelper()->tableExists(['ohrm_claim_event'])) {
            $this->getSchemaHelper()->createTable('ohrm_claim_event')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::TEXT, ['Notnull' => true,'Length'=>100])
                ->addColumn('description', Types::TEXT, ['Notnull' => false,'Length'=>1000])
                ->addColumn('added_by', Types::INTEGER, ['Notnull' => false])
                ->addColumn('status', Types::BOOLEAN, ['Notnull' => false])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['added_by'],
                'ohrm_user',
                ['id'],
                'addedBy',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_event', $foreignKeyConstraint);
        }

        $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_module')
            ->values(
                [
                    'name' => ':name',
                    'status' => ':status',
                    'display_name'=> ':display_name'
                ]
            )
            ->setParameter('name', "claim")
            ->setParameter('status', 1)
            ->setParameter('display_name', 'Claim')
            ->executeQuery();

        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');

        if (!$this->getSchemaHelper()->tableExists(['ohrm_expense_type'])) {
            $this->getSchemaHelper()->createTable('ohrm_expense_type')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::TEXT, ['Notnull' => true,'Length'=>100])
                ->addColumn('description', Types::TEXT, ['Notnull' => false,'Length'=>1000])
                ->addColumn('added_by', Types::INTEGER, ['Notnull' => false])
                ->addColumn('status', Types::BOOLEAN, ['Notnull' => false])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['added_by'],
                'ohrm_user',
                ['id'],
                'addedByUser',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_expense_type', $foreignKeyConstraint);
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_expense'])) {
            $this->getSchemaHelper()->createTable('ohrm_expense')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('expense_type_id', Types::INTEGER, ['Length' => 11])
                ->addColumn('date', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->addColumn('amount', Types::DECIMAL, ['Notnull' => false])
                ->addColumn('note', Types::TEXT, ['Notnull' => false,'Length'=>1000])
                ->addColumn('request_id', Types::INTEGER, ['Notnull' => false,'Length' => 11])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['expense_type_id'],
                'ohrm_expense_type',
                ['id'],
                'expenseTypeId',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_expense', $foreignKeyConstraint);
        }
    }
    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.4.0';
    }
}
