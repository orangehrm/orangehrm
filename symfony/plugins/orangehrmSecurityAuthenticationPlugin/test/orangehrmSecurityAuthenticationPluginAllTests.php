<?php



class orangehrmSecurityAuthenticationPluginAllTests {

    public static function suite() {

        $suite = new PHPUnit\Framework\TestSuite('orangehrmSecurityAuthenticationPluginAllTest');

        $basePath = dirname(__FILE__);

        $suite->addTestFile($basePath . '/utility/SecurityAuthenticationConfigServiceTest.php');
        $suite->addTestFile($basePath . '/utility/PasswordHelperTest.php');
        $suite->addTestFile($basePath . '/model/dao/PasswordResetDaoTest.php');
        $suite->addTestFile($basePath . '/model/service/PasswordResetServiceTest.php');
        return $suite;
    }
}


