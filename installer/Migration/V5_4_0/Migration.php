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
use OrangeHRM\Installer\Util\Logger;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

//use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');

        if (!$this->getSchemaHelper()->tableExists(['ohrm_claim_event'])) {

            $this->getSchemaHelper()->createTable('ohrm_claim_event')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::TEXT, ['Notnull' => false])
                ->addColumn('description', Types::TEXT, ['Notnull' => false])
                ->addColumn('added_by', Types::INTEGER, ['Notnull' => false])
                ->addColumn('status', Types::STRING, ['Length' => 65, 'Notnull' => false])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['added_by'],
                'ohrm_user',
                ['id'],
                'added_by',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_event', $foreignKeyConstraint);
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_expense_type'])) {
            $this->getSchemaHelper()->createTable('ohrm_expense_type')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::TEXT, ['Notnull' => false])
                ->addColumn('description', Types::TEXT, ['Notnull' => false])
                ->addColumn('added_by', Types::INTEGER, ['Notnull' => false])
                ->addColumn('status', Types::STRING, ['Length' => 65, 'Notnull' => false])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['added_by'],
                'ohrm_user',
                ['id'],
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_expense_type', $foreignKeyConstraint);
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_claim_request'])) {
            $this->getSchemaHelper()->createTable('ohrm_claim_request')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('emp_number', Types::INTEGER)
                ->addColumn('added_by', Types::INTEGER, ['Notnull' => false])
                ->addColumn('reference_id', Types::TEXT, ['Notnull' => false])
                ->addColumn('event_type_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('description', Types::TEXT, ['Notnull' => false])
                ->addColumn('currency', Types::STRING, ['Length' => 3, 'Notnull' => false])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->addColumn('status', Types::TEXT, ['Notnull' => false])
                ->addColumn('created_date', Types::DATE_MUTABLE, ['Notnull' => false])
                ->addColumn('submitted_date', Types::DATE_MUTABLE, ['Notnull' => false])
                ->setPrimaryKey(['id'])
                ->create();
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['added_by'],
                'ohrm_user',
                ['id'],
                ['onDelete' => 'SET NULL']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint);
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['event_type_id'],
                'ohrm_claim_event',
                ['id'],
                ['onDelete' => 'SET NULL']
            );

            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint);
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['emp_number'],
                'hs_hr_employee',
                ['emp_number'],
                ['onDelete' => 'SET NULL']
            );

            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request', $foreignKeyConstraint);
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_expense'])) {
            $this->getSchemaHelper()->createTable('ohrm_expense')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('expense_type_id', Types::INTEGER,)
                ->addColumn('date', Types::DATE_MUTABLE, ['Notnull' => false])
                ->addColumn('amount', Types::DECIMAL, ['Notnull' => false])
                ->addColumn('note', Types::TEXT, ['Notnull' => false])
                ->addColumn('request_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('is_deleted', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->setPrimaryKey(['id'])
                ->create();

            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['request_id'],
                'ohrm_claim_request',
                ['id'],
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_expense', $foreignKeyConstraint);
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['expense_type_id'],
                'ohrm_expense_type',
                ['id'],
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_expense', $foreignKeyConstraint);
        }


        if (!$this->getSchemaHelper()->tableExists(['ohrm_claim_attachment'])) {
            $this->getSchemaHelper()->createTable('ohrm_claim_attachment')
                ->addColumn('request_id', Types::INTEGER)
                ->addColumn('eattach_id', Types::BIGINT)
                ->addColumn('eattach_size', Types::INTEGER, ['Default' => 0])
                ->addColumn('eattach_desc', Types::STRING, ['Length' => 1000, 'Notnull' => false])
                ->addColumn('eattach_filename', Types::STRING, ['Length' => 1000, 'Notnull' => false])
                ->addColumn('eattach_attachment', Types::BLOB, ['Notnull' => false])
                ->addColumn('eattach_type', Types::STRING, ['Length' => 200, 'Notnull' => false])
                ->addColumn('attached_by', Types::INTEGER, ['Default' => null])
                ->addColumn('attached_by_name', Types::STRING, ['Length' => 200, 'Notnull' => false])
                ->addColumn('attached_time', Types::DATE_MUTABLE, ['Notnull' => false])
                ->setPrimaryKey(['request_id', 'eattach_id'])
                ->create();

            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['attached_by'],
                'hs_hr_employee',
                ['emp_number'],
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_attachment', $foreignKeyConstraint);
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['request_id'],
                'ohrm_claim_request',
                ['id'],
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_attachment', $foreignKeyConstraint);
        }
        if (!$this->getSchemaHelper()->tableExists(['ohrm_claim_request_action_log'])) {
            $this->getSchemaHelper()->createTable('ohrm_claim_request_action_log')
                ->addColumn('id', Types::BIGINT, ['Autoincrement' => true])
                ->addColumn('claim_request_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('performed_by_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('action', Types::STRING, ['Length' => 255, 'Notnull' => false])
                ->addColumn('note', Types::STRING, ['Length' => 1000, 'Notnull' => false])
                ->addColumn('date_time', Types::DATETIME_MUTABLE)
                ->setPrimaryKey(['id'])
                ->create();

            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['performed_by_id'],
                'ohrm_user',
                ['id'],
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request_action_log', $foreignKeyConstraint);
            $foreignKeyConstraint = new ForeignKeyConstraint(
                ['claim_request_id'],
                'ohrm_claim_request',
                ['id'],
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_claim_request_action_log', $foreignKeyConstraint);
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
