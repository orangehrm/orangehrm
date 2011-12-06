<?php
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
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/OrganizationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/CompanyStructureDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/ProjectDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/JobTitleDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/CustomerDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/LocationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/EmploymentStatusDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/SkillDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/MembershipDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/NationalityDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/PayGradeDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/JobCategoryDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/EmailNotificationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/dao/WorkShiftDaoTest.php');
      

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/LocalizationServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/PimCsvDataImportServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/CompanyStructureServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/JobTitleServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/CustomerServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/ProjectServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/LocationServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/EmploymentStatusServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/MembershipServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/NationalityServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/PayGradeServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/JobCategoryServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/../plugins/orangehrmAdminPlugin/test/model/service/WorkShiftServiceTest.php');

        return $suite;

    }

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
}

