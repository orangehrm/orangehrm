<?php

/**
 * Base test class for all functional tests
 */
abstract class {baseTestClassName} extends sfBasePhpunitFunctionalTestCase
{
    /**
     * SetUp
     */
    final public function setUp()
    {
        parent::setUp();

        // Your code
    }


    /**
     * TearDown
     */
    final public function tearDown()
    {
        // Your code

        parent::tearDown();
    }


    /**
     * Returns database connection to wrap tests with transaction
     */
    protected function getConnection()
    {
        // return Doctrine_Manager::getInstance()->getConnection('doctrine');
    }


    /**
     * Inject your own functional testers
     *
     * @see sfTestFunctionalBase::setTesters()
     *
     * @return array
     *          'request'  => 'sfTesterRequest',
     *          'response' => 'sfTesterResponse',
     *          'user'     => 'sfTesterUser',
     */
    protected function getFunctionalTesters()
    {
        return array();
    }

}
