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

namespace OrangeHRM\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use OrangeHRM\Config\Config;
use OrangeHRM\ORM\Exception\ConfigNotFoundException;
use OrangeHRM\ORM\Functions\TimeDiff;

class Doctrine
{
    /**
     * @var null|Doctrine
     */
    private static ?Doctrine $instance = null;
    /**
     * @var null|EntityManager
     */
    private static ?EntityManager $entityManager = null;

    /**
     * @throws ConfigNotFoundException
     */
    private function __construct()
    {
        // TODO::fine tune doctrine with cache
        $isDevMode = false;
        $proxyDir = Config::get(Config::DOCTRINE_PROXY_DIR);
        $cache = null;
        $useSimpleAnnotationReader = false;
        $paths = $this->getPaths();
        $config = Setup::createAnnotationMetadataConfiguration(
            $paths,
            $isDevMode,
            $proxyDir,
            $cache,
            $useSimpleAnnotationReader
        );
        $config->setAutoGenerateProxyClasses(true);
        $config->addCustomStringFunction('TIME_DIFF', TimeDiff::class);

        if (!Config::isInstalled()) {
            throw new ConfigNotFoundException('Application not installed');
        }

        $conf = Config::getConf();
        $dbUser = $conf->getDbUser();
        $dbPass = $conf->getDbPass();
        $dbHost = $conf->getDbHost();
        $dbPort = $conf->getDbPort();
        $dbName = $conf->getDbName();
        $conn = [
            'driver' => 'pdo_mysql',
            'url' => "mysql://$dbUser:$dbPass@$dbHost:$dbPort/$dbName",
            'charset' => 'utf8mb4'
        ];

        self::$entityManager = EntityManager::create($conn, $config);
        self::$entityManager->getConnection()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * @return array
     */
    private function getPaths(): array
    {
        $paths = [];
        $pluginPaths = Config::get('ohrm_plugin_paths');
        foreach ($pluginPaths as $pluginPath) {
            $entityPath = realpath($pluginPath . '/entity');
            if ($entityPath) {
                $paths[] = $entityPath;
            }
        }
        return $paths;
    }

    /**
     * @return Doctrine
     */
    protected static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return EntityManager
     */
    public static function getEntityManager(): EntityManager
    {
        self::getInstance();
        return self::$entityManager;
    }
}
