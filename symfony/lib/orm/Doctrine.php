<?php

namespace OrangeHRM\ORM;

use Conf;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use OrangeHRM\Config\Config;

class Doctrine
{
    /**
     * @var null|Doctrine
     */
    private static $instance = null;
    /**
     * @var null|EntityManager
     */
    private static $entityManager = null;

    /**
     * @throws ORMException
     */
    private function __construct()
    {
        $isDevMode = true;
        $proxyDir = null;
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

        //TODO
        require_once realpath(__DIR__ . '/../../../lib/confs/Conf.php');
        $conf = new Conf();
        $conn = [
            'driver' => 'pdo_mysql',
            'url' => "mysql://{$conf->dbuser}:{$conf->dbpass}@{$conf->dbhost}:{$conf->dbport}/{$conf->dbname}",
            'charset' => 'utf8mb4'
        ];

        self::$entityManager = EntityManager::create($conn, $config);
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
