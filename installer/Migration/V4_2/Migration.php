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

namespace OrangeHRM\Installer\Migration\V4_2;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getSchemaHelper()->addColumn(
            'hs_hr_employee',
            'purged_at',
            Types::DATETIMETZ_MUTABLE,
            ['Notnull' => false, 'Default' => null]
        );

        $this->getSchemaHelper()
            ->addColumn(
                'ohrm_job_candidate',
                'consent_to_keep_data',
                Types::BOOLEAN,
                ['Notnull' => true, 'Default' => false]
            );

        $this->createQueryBuilder()
            ->insert('ohrm_module')
            ->values(
                [
                    'name' => ':name',
                    'status' => ':status'
                ]
            )
            ->setParameter('name', 'maintenance')
            ->setParameter('status', 1)
            ->executeQuery();

        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');

        $this->insertMenuItems(
            'Maintenance',
            $this->getScreenId('purgeEmployee'),
            null,
            1,
            1200,
            null,
            1
        );

        $maintainenceMenuId = $this->createQueryBuilder()
            ->select('menu_item.id')
            ->from('ohrm_menu_item', 'menu_item')
            ->where('menu_item.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Maintenance')
            ->executeQuery()
            ->fetchOne();

        $this->insertMenuItems(
            'Purge Records',
            null,
            $maintainenceMenuId,
            2,
            100,
            null,
            1
        );

        $this->insertMenuItems(
            'Access Records',
            $this->getScreenId('accessEmployeeData'),
            $maintainenceMenuId,
            2,
            200,
            null,
            1
        );

        $purgeRecordsMenuId = $this->createQueryBuilder()
            ->select('menu_item.id')
            ->from('ohrm_menu_item', 'menu_item')
            ->where('menu_item.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Purge Records')
            ->executeQuery()
            ->fetchOne();

        $this->insertMenuItems(
            'Employee Records',
            $this->getScreenId('purgeEmployee'),
            $purgeRecordsMenuId,
            3,
            200,
            null,
            1
        );

        $this->insertMenuItems(
            'Candidate Records',
            $this->getScreenId('purgeCandidateData'),
            $purgeRecordsMenuId,
            3,
            200,
            null,
            1
        );
    }

    /**
     * @param string $menuTitle
     * @param string|null $screenId
     * @param int|null $parentId
     * @param int $level
     * @param int $order_hint
     * @param string|null $urlExtras
     * @param int $status
     * @return void
     * @throws Exception
     */
    private function insertMenuItems(
        string  $menuTitle,
        ?string $screenId,
        ?int    $parentId,
        int     $level,
        int     $order_hint,
        ?string $urlExtras,
        int     $status
    ): void {
        $this->createQueryBuilder()
            ->insert('ohrm_menu_item')
            ->values(
                [
                    'menu_title' => ':menuTitle',
                    'screen_id' => ':screenId',
                    'parent_id' => ':ParentId',
                    'level' => ':level',
                    'order_hint' => ':orderHint',
                    'url_extras' => ':urlExtras',
                    'status' => 'status'
                ]
            )
            ->setParameter('menuTitle', $menuTitle)
            ->setParameter('screenId', $screenId)
            ->setParameter('ParentId', $parentId)
            ->setParameter('level', $level)
            ->setParameter('orderHint', $order_hint)
            ->setParameter('urlExtras', $urlExtras)
            ->setParameter('status', $status)
            ->executeQuery();
    }

    /**
     * @param string $actionUrl
     * @return int
     */
    private function getScreenId(string $actionUrl): int
    {
        $screenId = $this->getConnection()->createQueryBuilder()
            ->select('screen.id')
            ->from('ohrm_screen', 'screen')
            ->where('screen.action_url = :actionUrl')
            ->setParameter('actionUrl', $actionUrl)
            ->executeQuery()
            ->fetchOne();
        return $screenId;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.2';
    }
}
