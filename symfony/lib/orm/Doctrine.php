<?php

namespace OrangeHRM\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;

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
        //TODO
        $paths = [
            realpath(__DIR__ . '/../../plugins/orangehrmAdminPlugin/entity'),
            realpath(__DIR__ . '/../../plugins/orangehrmPerformancePlugin/entity'),
            realpath(__DIR__ . '/../../plugins/orangehrmPimPlugin/entity')
        ];
        $config = Setup::createAnnotationMetadataConfiguration(
            $paths,
            $isDevMode,
            $proxyDir,
            $cache,
            $useSimpleAnnotationReader
        );

        //TODO
        require_once realpath(__DIR__ . '/../../../lib/confs/Conf.php');
        $conf = new \Conf();
        $conn = [
            'driver' => 'pdo_mysql',
            'url' => "mysql://{$conf->dbuser}:{$conf->dbpass}@{$conf->dbhost}:{$conf->dbport}/{$conf->dbname}",
            'charset' => 'utf8mb4'
        ];

        self::$entityManager = EntityManager::create($conn, $config);
    }

    /**
     * @return Doctrine
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return EntityManager
     */
    public static function getEntityManager()
    {
        self::getInstance();
        return self::$entityManager;
    }
}
