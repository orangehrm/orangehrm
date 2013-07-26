<?php

define('SF_APP_NAME', 'upgrader');
define('SF_ENV', 'test');
define('SF_CONN', 'doctrine');

if (!defined('TEST_ENV_CONFIGURED')) {

    require_once(dirname(__FILE__) . '/../config/ProjectConfiguration.class.php');
    UpgraderTests::$configuration = ProjectConfiguration::getApplicationConfiguration(SF_APP_NAME, SF_ENV, true);
    sfContext::createInstance(UpgraderTests::$configuration);

    define('TEST_ENV_CONFIGURED', TRUE);
}


class UpgraderTests {

    public static $configuration = null;
    public static $databaseManager = null;
    public static $connection = null;

    protected function setUp() {

        if (self::$configuration) {
            // initialize database manager
            self::$databaseManager = new sfDatabaseManager(self::$configuration);
            self::$databaseManager->loadConfiguration();

            if (SF_CONN != '') {
                self::$connection = self::$databaseManager->getDatabase(SF_CONN);
            }
        }
    }

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');
        $suite->addTestFile(dirname(__FILE__) . '/schemaIncrementTasks/SchemaIncrementTask55Test.php');
        $suite->addTestFile(dirname(__FILE__) . '/schemaIncrementTasks/SchemaIncrementTask58Test.php');

        return $suite;
    }

}
