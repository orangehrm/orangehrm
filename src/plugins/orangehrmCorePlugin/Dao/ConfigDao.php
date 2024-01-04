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

namespace OrangeHRM\Core\Dao;

use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Entity\Config;

class ConfigDao extends BaseDao
{
    use LoggerTrait;

    /**
     * Set $key to given $value
     * @param string $name Key
     * @param string $value Value
     */
    public function setValue(string $name, string $value): Config
    {
        $config = $this->getRepository(Config::class)->find($name);

        if (!$config instanceof Config) {
            $config = new Config();
        }
        $config->setName($name);
        $config->setValue($value);
        $this->persist($config);
        return $config;
    }

    /**
     * Get value corresponding to given $key
     * @param string $name Key
     * @return string|null value
     */
    public function getValue(string $name): ?string
    {
        $config = $this->getRepository(Config::class)->find($name);
        if ($config instanceof Config) {
            return $config->getValue();
        }
        return null;
    }

    /**
     * @return array
     */
    public function getAllValues(): array
    {
        $values = [];
        $configs = $this->getRepository(Config::class)->findAll();
        foreach ($configs as $config) {
            $values[$config->getName()] = $config->getValue();
        }
        return $values;
    }
}
