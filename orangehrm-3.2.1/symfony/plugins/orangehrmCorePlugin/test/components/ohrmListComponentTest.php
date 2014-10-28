<?php


require_once '/var/www/html/orangehrm/symfony/plugins/orangehrmCorePlugin/modules/core/actions/ohrmListComponent.class.php';

/**
 * Test class for ohrmListComponent.
 * @group Core
 * @group ListComponent
 */
class ohrmListComponentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ohrmListComponent
     */
    protected $component;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $browser = new sfTestFunctional(new sfBrowser());
        $this->browser = $browser;
        $_SESSION = unserialize('a:28:{s:10:"styleSheet";s:6:"orange";s:5:"WPATH";s:10:"/orangehrm";s:4:"ldap";s:8:"disabled";s:10:"ldapStatus";s:8:"disabled";s:13:"printBenefits";s:7:"enabled";s:18:"userTimeZoneOffset";s:3:"5.5";s:30:"symfony/user/sfUser/attributes";a:1:{s:30:"symfony/user/sfUser/attributes";a:4:{s:7:"isLogin";b:0;s:4:"user";N;s:8:"userRole";N;s:22:"ohrmComponent.editMode";b:1;}}s:27:"symfony/user/sfUser/culture";s:2:"en";s:4:"user";s:6:"USR001";s:9:"userGroup";s:6:"USG001";s:7:"isAdmin";s:3:"Yes";s:5:"empID";N;s:9:"empNumber";N;s:5:"fname";s:5:"admin";s:12:"isSupervisor";b:0;s:14:"isProjectAdmin";b:0;s:9:"isManager";b:0;s:10:"isDirector";b:0;s:10:"isAcceptor";b:0;s:9:"isOfferer";b:0;s:4:"path";s:23:"/var/www/html/orangehrm";s:13:"timePeriodSet";s:3:"Yes";s:11:"localRights";a:5:{s:3:"add";b:1;s:4:"edit";b:1;s:6:"delete";b:1;s:4:"view";b:1;s:6:"repDef";b:1;}s:13:"PIM_MENU_TYPE";s:4:"left";s:6:"posted";b:0;s:7:"hp-role";s:5:"Admin";s:9:"hp-module";s:5:"Admin";s:9:"hp-action";s:3:"NAT";}');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testExecute().
     */
    public function testExecute()
    {
        
        $this->browser->get('core/index')
                ->with('response')
                ->begin()
                ->isStatusCode(200)
                ->checkElement('body', '/Core Index/')
                ->checkElement('h2', 'Nationality & Race : Nationality')
                ->end();
    }
}
?>
