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

namespace OrangeHRM\Tests\Core\Essential;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;

/**
 * @group Core
 * @group Essential
 */
class UniqueRouteKeyTest extends TestCase
{
    public function testUniqueRouteKey(): void
    {
        $locator = new FileLocator();
        $yamlFileLoader = new YamlFileLoader($locator);

        $routesKeys = [];
        $inUseRoutesKeys = [];
        $plugins = Config::get(Config::PLUGINS);
        foreach ($plugins as $plugin) {
            $routePath = realpath(Config::get(Config::PLUGINS_DIR) . '/' . $plugin . '/config/routes.yaml');
            if ($routePath) {
                $pluginRoutes = $yamlFileLoader->load($routePath);
                foreach ($pluginRoutes->all() as $key => $route) {
                    if (isset($routesKeys[$key])) {
                        $inUseRoutesKeys[$key] = $routePath;
                    } else {
                        $routesKeys[$key] = $route;
                    }
                }
            }
        }

        if (empty($inUseRoutesKeys)) {
            $this->assertTrue(true);
        } else {
            $inUseRoutesKeys = array_map(
                function ($k, $v) {
                    return "{$k} => {$v} ";
                },
                array_keys($inUseRoutesKeys),
                $inUseRoutesKeys
            );
            $inUseRouteKeys = implode(", \n", $inUseRoutesKeys);
            throw new Exception(
                "Following route keys already in use;\n\n" .
                $inUseRouteKeys . "\n\n" . str_repeat('_ ', 20)
            );
        }
    }
}
