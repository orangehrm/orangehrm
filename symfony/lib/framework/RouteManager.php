<?php

namespace OrangeHRM\Framework;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class RouteManager
{
    /**
     * @var null|RouteCollection
     */
    private static $routes = null;

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
            $plugins = [
                'orangehrmAdminPlugin',
                'orangehrmAttendancePlugin',
                // ...
            ];

            foreach ($plugins as $plugin) {
                //TODO::move to config such as sfConfig::get('sf_plugins_dir')
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
