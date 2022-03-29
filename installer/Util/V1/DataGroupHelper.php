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

namespace OrangeHRM\Installer\Util\V1;

use Doctrine\DBAL\Connection;
use OrangeHRM\Installer\Util\V1\Dto\Api;
use Symfony\Component\Yaml\Yaml;

class DataGroupHelper
{
    private Connection $connection;
    private array $moduleIds = [];
    private array $userRoleIds = [];

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param string $filepath
     * @return Api[]
     */
    protected function readApiPermissions(string $filepath): array
    {
        $apiPermissions = [];
        foreach (Yaml::parseFile($filepath) as $name => $dataGroup) {
            $apiPermissions[] = Api::createFromArray($name, $dataGroup);
        }
        return $apiPermissions;
    }

    /**
     * @param string $filepath
     */
    public function insertApiPermissions(string $filepath): void
    {
        $apiPermissions = $this->readApiPermissions($filepath);
        foreach ($apiPermissions as $apiPermission) {
            $qb = $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_data_group')
                ->values(
                    [
                        'name' => ':name',
                        'description' => ':description',
                        'can_read' => ':read',
                        'can_create' => ':create',
                        'can_update' => ':update',
                        'can_delete' => ':delete',
                    ]
                )
                ->setParameter('name', $apiPermission->getName())
                ->setParameter('description', $apiPermission->getDescription())
                ->setParameter('read', $apiPermission->getAllowed()->canRead())
                ->setParameter('create', $apiPermission->getAllowed()->canCreate())
                ->setParameter('update', $apiPermission->getAllowed()->canUpdate())
                ->setParameter('delete', $apiPermission->getAllowed()->canDelete());
            $qb->executeQuery();

            $qb = $this->getConnection()->createQueryBuilder()
                ->select('dataGroup.id')
                ->from('ohrm_data_group', 'dataGroup')
                ->where('dataGroup.name = :dataGroupName')
                ->setParameter('dataGroupName', $apiPermission->getName())
                ->setMaxResults(1);
            $id = $qb->fetchOne();

            $qb = $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_api_permission')
                ->values(
                    [
                        'api_name' => ':api',
                        'module_id' => ':moduleId',
                        'data_group_id' => ':dataGroupId',
                    ]
                )
                ->setParameter('api', $apiPermission->getApi())
                ->setParameter('moduleId', $this->getModuleId($apiPermission->getModule()))
                ->setParameter('dataGroupId', $id);
            $qb->executeQuery();

            foreach ($apiPermission->getPermissions() as $permission) {
                $qb = $this->getConnection()->createQueryBuilder()
                    ->insert('ohrm_user_role_data_group')
                    ->values(
                        [
                            'data_group_id' => ':dataGroupId',
                            'user_role_id' => ':userRoleId',
                            'can_read' => ':read',
                            'can_create' => ':create',
                            'can_update' => ':update',
                            'can_delete' => ':delete',
                            'self' => ':self',
                        ]
                    )
                    ->setParameter('dataGroupId', $id)
                    ->setParameter('userRoleId', $this->getUserRoleId($permission->getUserRole()))
                    ->setParameter('read', $permission->canRead())
                    ->setParameter('create', $permission->canCreate())
                    ->setParameter('update', $permission->canUpdate())
                    ->setParameter('delete', $permission->canDelete())
                    ->setParameter('self', $permission->isSelf());
                $qb->executeQuery();
            }
        }
    }

    /**
     * @param string $moduleName
     * @return int
     */
    protected function getModuleId(string $moduleName): int
    {
        if (!isset($this->moduleIds[$moduleName])) {
            $qb = $this->getConnection()->createQueryBuilder()
                ->select('module.id')
                ->from('ohrm_module', 'module')
                ->where('module.name = :moduleName')
                ->setParameter('moduleName', $moduleName)
                ->setMaxResults(1);
            $this->moduleIds[$moduleName] = $qb->fetchOne();
        }
        return $this->moduleIds[$moduleName];
    }

    /**
     * @param string $userRoleName
     * @return int
     */
    protected function getUserRoleId(string $userRoleName): int
    {
        if (!isset($this->userRoleIds[$userRoleName])) {
            $qb = $this->getConnection()->createQueryBuilder()
                ->select('userRole.id')
                ->from('ohrm_user_role', 'userRole')
                ->where('userRole.name = :userRoleName')
                ->setParameter('userRoleName', $userRoleName)
                ->setMaxResults(1);
            $this->userRoleIds[$userRoleName] = $qb->fetchOne();
        }
        return $this->userRoleIds[$userRoleName];
    }
}
