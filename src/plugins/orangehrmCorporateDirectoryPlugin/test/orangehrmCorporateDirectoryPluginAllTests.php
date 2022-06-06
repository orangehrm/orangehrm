<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestSuite;

class orangehrmCorporateDirectoryPluginAllTests
{
    public static function suite(): TestSuite
    {

        $suite = new TestSuite('orangehrmCorporateDirectoryPlugin');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/test/Dao/EmployeeDirectoryDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/test/Service/EmployeeDirectoryServiceTest.php');

        return $suite;
    }
}
