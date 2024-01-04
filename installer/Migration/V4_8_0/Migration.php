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

namespace OrangeHRM\Installer\Migration\V4_8_0;

use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->insertConfig('help.url', 'https://opensourcehelp.orangehrm.com'); //has access issues.
        $this->insertConfig('help.processorClass', 'ZendeskHelpProcessor');

        $this->createQueryBuilder()
            ->insert('ohrm_i18n_group')
            ->values(
                [
                    'name' => ':name',
                    'title' => ':title'
                ]
            )
            ->setParameter('name', 'help')
            ->setParameter('title', 'Help')
            ->executeQuery();
    }

    /**
     * @param string $value
     * @param string $key
     * @return void
     */
    private function insertConfig(string $key, string $value): void
    {
        $this->createQueryBuilder()
            ->insert('hs_hr_config')
            ->values(
                [
                    '`key`' => ':key',
                    'value' => ':value'
                ]
            )
            ->setParameter('key', $key)
            ->setParameter('value', $value)
            ->executeQuery();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.8';
    }

    /**
     * @param string $value
     * @param string $key
     * @return void
     */
    private function updateConfig(string $value, string $key): void
    {
        $this->createQueryBuilder()
            ->update('hs_hr_config', 'config')
            ->set('config.value', ':value')
            ->setParameter('value', $value)
            ->andWhere('config.key = :key')
            ->setParameter('key', $key)
            ->executeQuery();
    }
}
