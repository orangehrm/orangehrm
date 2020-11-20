<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;

class Doctrine2
{
    /**
     * @var null|Doctrine2
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
        $config = Setup::createAnnotationMetadataConfiguration(
            [__DIR__ . "/doctrine2"],
            $isDevMode,
            $proxyDir,
            $cache,
            $useSimpleAnnotationReader
        );

        $conf = OrangeConfig::getInstance()->getConf();
        $conn = [
            'driver' => 'pdo_mysql',
            'url' => "mysql://{$conf->dbuser}:{$conf->dbpass}@{$conf->dbhost}:{$conf->dbport}/{$conf->dbname}",
            'charset' => 'utf8mb4'
        ];

        self::$entityManager = EntityManager::create($conn, $config);
    }

    /**
     * @return Doctrine2
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
