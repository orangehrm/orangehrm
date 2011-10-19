<?php
require_once 'PHPUnit/Framework.php';
require_once 'util/TestDataService.php';

if (!defined('ROOT_PATH')) {
    define( 'ROOT_PATH', dirname(__FILE__) . '/../../' );
}
if (!defined('SF_APP_NAME')) {
    define('SF_APP_NAME', 'orangehrm' );
}
if (!defined('SF_ENV')) {
    define('SF_ENV', 'test' );
}
if (!defined('SF_CONN')) {
    define('SF_CONN', 'doctrine' );
}

if (!defined('TEST_ENV_CONFIGURED')) {
    
    require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
    PluginAllTests::$configuration = ProjectConfiguration::getApplicationConfiguration( SF_APP_NAME , SF_ENV, true);
    sfContext::createInstance(PluginAllTests::$configuration);

    define('TEST_ENV_CONFIGURED', TRUE);
}

class PluginAllTests
{
    public static $configuration = null;
    public static $databaseManager = null;
    public static $connection = null;

    protected function setUp()
    {

        if ( self::$configuration )
        {
            // initialize database manager
            self::$databaseManager = new sfDatabaseManager(self::$configuration);
            self::$databaseManager->loadConfiguration();

            if ( SF_CONN != '' ) self::$connection = self::$databaseManager->getDatabase( SF_CONN );
        }

    }


    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('AdminModuleAllTest');

        /* Dao Test Cases */
        //$suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmRecruitmentPlugin/test/model/dao/CandidateDaoTest.php');
      

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/LocalizationServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/PimCsvDataImportServiceTest.php');

        return $suite;

    }

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
}

?>
