<?php

require_once 'PHPUnit/Framework.php';
require_once 'util/TestDataService.php';
define('ROOT_PATH', dirname(__FILE__) . '/../../');
define('SF_APP_NAME', 'orangehrm');
define('SF_ENV', 'test');
define('SF_CONN', 'doctrine');

if (!defined('TEST_ENV_CONFIGURED')) {

    require_once(dirname(__FILE__) . '/../config/ProjectConfiguration.class.php');
    AllTests::$configuration = ProjectConfiguration::getApplicationConfiguration(SF_APP_NAME, SF_ENV, true);
    sfContext::createInstance(AllTests::$configuration);

    define('TEST_ENV_CONFIGURED', TRUE);
}

class AllTests {

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

    
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');

        // execute validator unit tests
    	$coredir = new DirectoryIterator(dirname(__FILE__). '/validator');
        while ($coredir->valid()) {
            if (strpos( $coredir, 'Test.php' ) !== false) {
                $suite->addTestFile(  dirname(__FILE__). '/validator/'. $coredir );
            }
            $coredir->next();
        }

        $suite->addTestFile(dirname(__FILE__) . '/lib/utility/EncryptionListenerTest.php');

    	// execute action unit tests
    	$coredir = new DirectoryIterator(dirname(__FILE__). '/actions');
        while ($coredir->valid()) {
            if (strpos( $coredir, 'Test.php' ) !== false) {
                $suite->addTestFile(  dirname(__FILE__). '/actions/'. $coredir );
            }
            $coredir->next();
        }
        
        //execute core service classes		
        $coredir = new DirectoryIterator( dirname(__FILE__). '/model/core/service' );
        while($coredir->valid()) {
            if( strpos( $coredir, 'Test.php' ) !== false ) {
                $suite->addTestFile(  dirname(__FILE__). '/model/core/service/'. $coredir );
                
            }
            $coredir->next();
        }        
        
    	$dir = new DirectoryIterator( dirname(__FILE__). '/model/performance/dao' );
        while($dir->valid()) {
            if( strpos( $dir, 'Test.php' ) !== false ) {
                $suite->addTestFile(  dirname(__FILE__). '/model/performance/dao/'. $dir );
                
            }
            $dir->next();
        }
        
        //Execute performance service class
        
        $dir = new DirectoryIterator( dirname(__FILE__). '/model/performance/service' );
        while($dir->valid()) {
            if( strpos( $dir, 'Test.php' ) !== false ) {
                $suite->addTestFile(  dirname(__FILE__). '/model/performance/service/'. $dir );
                
            }
            $dir->next();
        }        

        $dir = new DirectoryIterator( dirname(__FILE__). '/model/pim/service' );
        while($dir->valid()) {
            if( strpos( $dir, 'Test.php' ) !== false ) {
                $suite->addTestFile(  dirname(__FILE__). '/model/pim/service/'. $dir );

            }
            $dir->next();
        }

        //
        // Add Plugin tests
        //
        //$suite->addTestFile(dirname(__FILE__) . '/PluginAllTests.php');
        
        return $suite;
    }

}
