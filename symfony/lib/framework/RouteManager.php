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

namespace OrangeHRM\Framework;

use OrangeHRM\Config\Config;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class RouteManager
{
    /**
     * @var null|RouteCollection
     */
    private static ?RouteCollection $routes = null;

    /**
     * @return RouteCollection
     */
    public static function getRoutes(): RouteCollection
    {
        if (is_null(self::$routes)) {
            $locator = new FileLocator();
            $yamlFileLoader = new YamlFileLoader($locator);
            self::$routes = new RouteCollection();

            //TODO:: move to resolve along with caching
            $plugins = Config::get('ohrm_plugins');
            foreach ($plugins as $plugin) {
                $routePath = realpath(__DIR__ . '/../../plugins/' . $plugin . '/config/routes.yaml');
                if ($routePath) {
                    $pluginRoutes = $yamlFileLoader->load($routePath);
                    self::$routes->addCollection($pluginRoutes);
                }
            }
            return self::$routes;
        }

        return self::$routes;
    }
}
