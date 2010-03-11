<?php
require_once dirname(__FILE__).'/../../bootstrap/all.php';


/**
 * ExampleModuleActionTest
 */
class ExampleModuleActionTest extends myFunctionalTestCase
{
    protected $app = 'frontend'; // frontend is default


    /**
     * SetUp
     */
    public function _start()
    {
    }


    /**
     * TearDown
     */
    public function _tearDown()
    {
    }


    /**
     * First test
     */
    public function test1()
    {
        $this->browser
            ->getAndCheck('home', 'index', '/', 200);
    }

}
