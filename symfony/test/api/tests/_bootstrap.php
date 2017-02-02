<?php
require_once dirname(__FILE__).'/../../util/TestDataService.php';

if (!defined('ROOT_PATH')) {
    define( 'ROOT_PATH', dirname(__FILE__) . '/../../../' );
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

    require_once(dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php');
    PluginAllTests::$configuration = ProjectConfiguration::getApplicationConfiguration( SF_APP_NAME , SF_ENV, true);
    sfContext::createInstance(PluginAllTests::$configuration);

    define('TEST_ENV_CONFIGURED', TRUE);
}
