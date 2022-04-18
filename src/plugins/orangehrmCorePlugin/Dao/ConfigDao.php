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

namespace OrangeHRM\Core\Dao;

use Exception;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Config;
use OrangeHRM\Framework\Logger;

/**
 * Config Dao: Manages configuration entries in hs_hr_config
 *
 */
class ConfigDao extends BaseDao
{
    /**
     * Set $key to given $value
     * @param string $name Key
     * @param string $value Value
     * @throws DaoException
     */
    public function setValue(string $name, string $value): Config
    {
        try {
            $config = $this->getRepository(Config::class)->find($name);

            if (!$config instanceof Config) {
                $config = new Config();
            }
            $config->setName($name);
            $config->setValue($value);
            $this->persist($config);
            return $config;
        } catch (Exception $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get value corresponding to given $key
     * @param string $name Key
     * @return string|null value
     */
    public function getValue(string $name): ?string
    {
        try {
            $config = $this->getRepository(Config::class)->find($name);
            if ($config instanceof Config) {
                return $config->getValue();
            }
            return null;
        } catch (Exception $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return array
     * @throws DaoException
     */
    public function getAllValues(): array
    {
        try {
            $values = [];
            $configs = $this->getRepository(Config::class)->findAll();
            foreach ($configs as $config) {
                $values[$config->getName()] = $config->getValue();
            }
            return $values;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
