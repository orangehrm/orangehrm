<?php

namespace OrangeHRM\Installer\Migration\V4_1_2_1;

use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        // no db changes in this version
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.1.2.1';
    }
}
