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
use OrangeHRM\Installer\Util\V1\Dto\LangString;
use Symfony\Component\Yaml\Yaml;

class LanguageHelper
{
    private Connection $connection;
    private array $groupIds = [];

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
     * @return LangString[]
     */
    public function readLangStrings(string $filepath, string $groupName): array
    {
        $langStrings = [];
        $groupId = $this->getGroupIdByName($groupName);
        foreach (Yaml::parseFile($filepath)['langStrings'] as $langString) {
            $langString['groupId'] = $groupId;
            $langStrings[] = LangString::createFromArray($langString);
        }
        return $langStrings;
    }

    /**
     * @param string $groupName
     * @return int
     */
    public function getGroupIdByName(string $groupName): int
    {
        if (!isset($this->groupIds[$groupName])) {
            $qb = $this->getConnection()->createQueryBuilder()
                ->select('i18nGroup.id')
                ->from('ohrm_i18n_group', 'i18nGroup')
                ->where('i18nGroup.name = :groupName')
                ->setParameter('groupName', $groupName)
                ->setMaxResults(1);
            $this->groupIds[$groupName] = $qb->fetchOne();
        }
        return $this->groupIds[$groupName];
    }
}
