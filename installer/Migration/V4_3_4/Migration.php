<?php

namespace OrangeHRM\Installer\Migration\V4_3_4;

use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        if (!$this->getSchemaHelper()->tableExists('ohrm_employee_subscription')) {
            $this->getSchemaHelper()->createTable('ohrm_employee_subscription')
                ->addColumn('id', Types::INTEGER, ['Unsigned' => true, 'Autoincrement' => true])
                ->addColumn('employee_id', Types::INTEGER, ['Length' => 7, 'Notnull' => true])
                ->addColumn('status', Types::SMALLINT, ['Length' => 6, 'Notnull' => true])
                ->addColumn('created_at', Types::DATETIME_MUTABLE, ['Notnull' => true])
                ->setPrimaryKey(['id'])
                ->create();
        }
    }
}
