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

namespace OrangeHRM\Installer\Migration\V4_3_2;

use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $q = $this->createQueryBuilder();
        $q->select('email_config.sendmail_path')
            ->from('ohrm_email_configuration', 'email_config')
            ->where('email_config.mail_type = :mailType')
            ->setParameter('mailType', 'sendmail')
            ->andWhere($q->expr()->isNotNull('email_config.sendmail_path'))
            ->andWhere('sendmail_path != :empty')
            ->setParameter('empty', '');
        $oldPath = $q->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->insert('hs_hr_config')
            ->values(
                [
                    '`key`' => ':key',
                    'value' => ':value'
                ]
            )
            ->setParameter('key', 'email_config.sendmail_path')
            ->setParameter('value', '/usr/sbin/sendmail -bs')
            ->executeQuery();

        if ($oldPath != "") {
            $this->createQueryBuilder()
                ->update('hs_hr_config', 'config')
                ->set('value', ':oldValue')
                ->setParameter('oldValue', $oldPath)
                ->where('key = :sendmailPath')
                ->setParameter('sendmailPath', 'email_config.sendmail_path')
                ->executeQuery();
        }

        $this->getSchemaHelper()->dropColumn('ohrm_email_configuration', 'sendmail_path');

        $this->getSchemaHelper()->addColumn('ohrm_marketplace_addon', 'type', Types::STRING, ['Notnull'=> false,'Default' => 'free']);
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.3.2';
    }
}
