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

namespace OrangeHRM\Framework;

use OrangeHRM\Framework\DependencyInjection\ContainerBuilder;

class ServiceContainer
{
    /**
     * @var self|null
     */
    private static ?ServiceContainer $instance = null;

    /**
     * @var ContainerBuilder|null
     */
    private static ?ContainerBuilder $containerBuilder = null;

    private function __construct()
    {
        self::$containerBuilder = new ContainerBuilder();
    }

    /**
     * @return static
     */
    protected static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return ContainerBuilder
     */
    public static function getContainer(): ContainerBuilder
    {
        self::getInstance();
        return self::$containerBuilder;
    }
}
