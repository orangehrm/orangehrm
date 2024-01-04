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

namespace OrangeHRM\Installer\Util\V1;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use OrangeHRM\Installer\Util\V1\Dto\Api;
use OrangeHRM\Installer\Util\V1\Dto\DataGroup;
use OrangeHRM\Installer\Util\V1\Dto\Screen;
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
     * @return DataGroup[]
     */
    protected function readDataGroupPermissions(string $filepath): array
    {
        $dataGroupPermissions = [];
        foreach (Yaml::parseFile($filepath) as $name => $dataGroup) {
            $dataGroupPermissions[] = DataGroup::createFromArray($name, $dataGroup);
        }
        return $dataGroupPermissions;
    }

    /**
     * @param string $filepath
     * @return Screen[]
     */
    protected function readScreenPermissions(string $filepath): array
    {
        $screenPermissions = [];
        foreach (Yaml::parseFile($filepath) as $name => $dataGroup) {
            $screenPermissions[] = Screen::createFromArray($name, $dataGroup);
        }
        return $screenPermissions;
    }

    /**
     * @param string $filepath
     */
    public function insertApiPermissions(string $filepath): void
    {
        $apiPermissions = $this->readApiPermissions($filepath);
        foreach ($apiPermissions as $apiPermission) {
            $this->getConnection()->createQueryBuilder()
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
                ->setParameter('read', $apiPermission->getAllowed()->canRead(), ParameterType::BOOLEAN)
                ->setParameter('create', $apiPermission->getAllowed()->canCreate(), ParameterType::BOOLEAN)
                ->setParameter('update', $apiPermission->getAllowed()->canUpdate(), ParameterType::BOOLEAN)
                ->setParameter('delete', $apiPermission->getAllowed()->canDelete(), ParameterType::BOOLEAN)
                ->executeQuery();

            $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_api_permission')
                ->values(
                    [
                        'api_name' => ':api',
                        'module_id' => ':moduleId',
                        'data_group_id' => ':dataGroupId',
                    ]
                )
                ->setParameter('api', $apiPermission->getApi())
                ->setParameter('moduleId', $this->getModuleIdByName($apiPermission->getModule()))
                ->setParameter('dataGroupId', $this->getDataGroupIdByName($apiPermission->getName()))
                ->executeQuery();

            foreach ($apiPermission->getPermissions() as $permission) {
                $this->addDataGroupPermissions(
                    $apiPermission->getName(),
                    $permission->getUserRole(),
                    $permission->canRead(),
                    $permission->canCreate(),
                    $permission->canUpdate(),
                    $permission->canDelete(),
                    $permission->isSelf()
                );
            }
        }
    }

    /**
     * @param string $filepath
     */
    public function insertDataGroupPermissions(string $filepath): void
    {
        $dataGroupPermissions = $this->readDataGroupPermissions($filepath);
        foreach ($dataGroupPermissions as $dataGroupPermission) {
            $this->getConnection()->createQueryBuilder()
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
                ->setParameter('name', $dataGroupPermission->getName())
                ->setParameter('description', $dataGroupPermission->getDescription())
                ->setParameter('read', $dataGroupPermission->getAllowed()->canRead(), ParameterType::BOOLEAN)
                ->setParameter('create', $dataGroupPermission->getAllowed()->canCreate(), ParameterType::BOOLEAN)
                ->setParameter('update', $dataGroupPermission->getAllowed()->canUpdate(), ParameterType::BOOLEAN)
                ->setParameter('delete', $dataGroupPermission->getAllowed()->canDelete(), ParameterType::BOOLEAN)
                ->executeQuery();

            foreach ($dataGroupPermission->getPermissions() as $permission) {
                $this->addDataGroupPermissions(
                    $dataGroupPermission->getName(),
                    $permission->getUserRole(),
                    $permission->canRead(),
                    $permission->canCreate(),
                    $permission->canUpdate(),
                    $permission->canDelete(),
                    $permission->isSelf()
                );
            }
        }
    }

    /**
     * @param string $filepath
     */
    public function insertScreenPermissions(string $filepath): void
    {
        $screenPermissions = $this->readScreenPermissions($filepath);
        foreach ($screenPermissions as $screenPermission) {
            $values = [
                'name' => ':name',
                'module_id' => ':moduleId',
                'action_url' => ':url',
            ];
            if (!is_null($screenPermission->getMenuConfigurator())) {
                $values['menu_configurator'] = ':menuConfigurator';
            }
            $qb = $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_screen')
                ->values($values)
                ->setParameter('name', $screenPermission->getName())
                ->setParameter('moduleId', $this->getModuleIdByName($screenPermission->getModule()))
                ->setParameter('url', $screenPermission->getUrl());
            if (!is_null($screenPermission->getMenuConfigurator())) {
                $qb->setParameter('menuConfigurator', $screenPermission->getMenuConfigurator());
            }
            $qb->executeQuery();

            $id = $this->getScreenIdByModuleAndUrl(
                $this->getModuleIdByName($screenPermission->getModule()),
                $screenPermission->getUrl()
            );

            foreach ($screenPermission->getPermissions() as $permission) {
                $this->getConnection()->createQueryBuilder()
                    ->insert('ohrm_user_role_screen')
                    ->values(
                        [
                            'screen_id' => ':screenId',
                            'user_role_id' => ':userRoleId',
                            'can_read' => ':read',
                            'can_create' => ':create',
                            'can_update' => ':update',
                            'can_delete' => ':delete',
                        ]
                    )
                    ->setParameter('screenId', $id)
                    ->setParameter('userRoleId', $this->getUserRoleIdByName($permission->getUserRole()))
                    ->setParameter('read', $permission->canRead(), ParameterType::BOOLEAN)
                    ->setParameter('create', $permission->canCreate(), ParameterType::BOOLEAN)
                    ->setParameter('update', $permission->canUpdate(), ParameterType::BOOLEAN)
                    ->setParameter('delete', $permission->canDelete(), ParameterType::BOOLEAN)
                    ->executeQuery();
            }
        }
    }

    /**
     * @param string $dataGroupName
     * @return int
     */
    public function getDataGroupIdByName(string $dataGroupName): int
    {
        $qb = $this->getConnection()->createQueryBuilder()
            ->select('dataGroup.id')
            ->from('ohrm_data_group', 'dataGroup')
            ->where('dataGroup.name = :dataGroupName')
            ->setParameter('dataGroupName', $dataGroupName)
            ->setMaxResults(1);
        return $qb->fetchOne();
    }

    /**
     * @param string $moduleId
     * @param string $url
     * @return int
     */
    public function getScreenIdByModuleAndUrl(string $moduleId, string $url): int
    {
        $qb = $this->getConnection()->createQueryBuilder()
            ->select('screen.id')
            ->from('ohrm_screen', 'screen')
            ->andWhere('screen.module_id = :moduleId')
            ->setParameter('moduleId', $moduleId)
            ->andWhere('screen.action_url = :url')
            ->setParameter('url', $url)
            ->setMaxResults(1);
        return $qb->fetchOne();
    }

    /**
     * @param string $moduleName
     * @return int
     */
    public function getModuleIdByName(string $moduleName): int
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
    public function getUserRoleIdByName(string $userRoleName): int
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

    /**
     * @param string $dataGroupName
     * @param string $userRoleName
     * @param bool $canRead
     * @param bool $canCreate
     * @param bool $canUpdate
     * @param bool $canDelete
     * @param bool $isSelf
     */
    public function addDataGroupPermissions(
        string $dataGroupName,
        string $userRoleName,
        bool $canRead = false,
        bool $canCreate = false,
        bool $canUpdate = false,
        bool $canDelete = false,
        bool $isSelf = false
    ): void {
        $this->getConnection()->createQueryBuilder()
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
            ->setParameter('dataGroupId', $this->getDataGroupIdByName($dataGroupName))
            ->setParameter('userRoleId', $this->getUserRoleIdByName($userRoleName))
            ->setParameter('read', $canRead, ParameterType::BOOLEAN)
            ->setParameter('create', $canCreate, ParameterType::BOOLEAN)
            ->setParameter('update', $canUpdate, ParameterType::BOOLEAN)
            ->setParameter('delete', $canDelete, ParameterType::BOOLEAN)
            ->setParameter('self', $isSelf, ParameterType::BOOLEAN)
            ->executeQuery();
    }
}
