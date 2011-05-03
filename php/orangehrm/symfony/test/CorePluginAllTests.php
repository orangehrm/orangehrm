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
    CorePluginAllTests::$configuration = ProjectConfiguration::getApplicationConfiguration( SF_APP_NAME , SF_ENV, true);
    sfContext::createInstance(CorePluginAllTests::$configuration);

    define('TEST_ENV_CONFIGURED', TRUE);
}

class CorePluginAllTests
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
        $suite = new PHPUnit_Framework_TestSuite('PluginAllTest');
        $config = new ProjectConfiguration();
        
        $enabledPlugins = $config->getPlugins();
        $pluginDirs     = new DirectoryIterator( dirname(__FILE__). '/../plugins' );
        $omitDirs       = array('.', '..', '.svn', 'apps', 'config', 'test');

        while($pluginDirs->valid()) {

            $path = dirname(__FILE__). '/../plugins/' .  $pluginDirs->getFilename() . "/test";

            if(file_exists($path) && is_dir($path) && !in_array($pluginDirs->getFilename(), $omitDirs)
                    && in_array($pluginDirs->getFilename(), $enabledPlugins)) {
                $handle = opendir($path);
                while (false !== ($file = readdir($handle))) {
                    if( strpos($file, 'orangehrmCorePluginAllTests.php' ) !== false) {
                        require_once($path . DIRECTORY_SEPARATOR . $file);
                        $suite->addTestSuite(substr($file, 0, -4));
                    }
                }
            }

            $pluginDirs->next();
        }
        return $suite;
    }

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
}

?>
