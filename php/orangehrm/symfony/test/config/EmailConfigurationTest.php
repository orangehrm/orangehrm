<?php
require_once 'PHPUnit/Framework.php';

class EmailConfigurationTest extends PHPUnit_Framework_TestCase {
    
    private $confPath;
    private $confCurrentContents;
    private $confExists = false;

    public function  __construct() {

        $this->confPath = sfConfig::get('sf_root_dir') . '/apps/orangehrm/config/emailConfiguration.yml';

        if (file_exists($this->confPath)) {
            $this->confExists = true;
        }

    }

    public function setup() {

        if ($this->confExists) {
            $this->confCurrentContents = file_get_contents($this->confPath);
        }

        $testContent = file_get_contents( sfConfig::get('sf_test_dir') . '/fixtures/config/EmailConfiguration.yml');
        file_put_contents($this->confPath, $testContent);

    }

    public function testConfInitialValues() {

        $emailConf = new EmailConfiguration();

        $this->assertEquals('smtp', $emailConf->getMailType());
        $this->assertEquals('example@example.com', $emailConf->getSentAs());
        $this->assertEquals('smtp.example.com', $emailConf->getSmtpHost());
        $this->assertEquals('222', $emailConf->getSmtpPort());
        $this->assertEquals('testUser', $emailConf->getSmtpUsername());
        $this->assertEquals('testPassword', $emailConf->getSmtpPassword());
        $this->assertEquals('LOGIN', $emailConf->getSmtpAuthType());
        $this->assertEquals('SSL', $emailConf->getSmtpSecurityType());
        $this->assertEquals('path/to/sendmail', $emailConf->getSendmailPath());

    }

    public function testSaveEmailConfigurationSmtpValues() {

        $emailConf = new EmailConfiguration();

        $emailConf->setMailType('smtp');
        $emailConf->setSentAs('example@example2.com');
        $emailConf->setSmtpHost('smtp.example2.com');
        $emailConf->setSmtpPort('333');
        $emailConf->setSmtpUsername('testUser2');
        $emailConf->setSmtpPassword('testPassword2');
        $emailConf->setSmtpAuthType('NO');
        $emailConf->setSmtpSecurityType('TLS');

        $emailConf->save();

        $savedConfData = sfYaml::load($this->confPath);

        $this->assertEquals('smtp', $savedConfData['mailType']);
        $this->assertEquals('example@example2.com', $savedConfData['sentAs']);
        $this->assertEquals('smtp.example2.com', $savedConfData['smtp']['host']);
        $this->assertEquals('333', $savedConfData['smtp']['port']);
        $this->assertEquals('testUser2', $savedConfData['smtp']['username']);
        $this->assertEquals('testPassword2', $savedConfData['smtp']['password']);
        $this->assertEquals('NO', $savedConfData['smtp']['authType']);
        $this->assertEquals('TLS', $savedConfData['smtp']['securityType']);

        /* Make sure unset data is not altered */
        $this->assertEquals('path/to/sendmail', $savedConfData['sendmail']['path']);

    }

    public function testSaveEmailConfigurationSendmailValues() {

        $emailConf = new EmailConfiguration();

        $emailConf->setMailType('sendmail');
        $emailConf->setSendmailPath('path/to/new/sendmail');

        $emailConf->save();

        $savedConfData = sfYaml::load($this->confPath);

        $this->assertEquals('sendmail', $savedConfData['mailType']);
        $this->assertEquals('path/to/new/sendmail', $savedConfData['sendmail']['path']);

        /* Make sure unset data is not altered */
        $this->assertEquals('example@example.com', $savedConfData['sentAs']);
        $this->assertEquals('smtp.example.com', $savedConfData['smtp']['host']);
        $this->assertEquals('222', $savedConfData['smtp']['port']);
        $this->assertEquals('testUser', $savedConfData['smtp']['username']);
        $this->assertEquals('testPassword', $savedConfData['smtp']['password']);
        $this->assertEquals('LOGIN', $savedConfData['smtp']['authType']);
        $this->assertEquals('SSL', $savedConfData['smtp']['securityType']);

    }

    public function teardown() {

        if ($this->confExists) {
            file_put_contents($this->confPath, $this->confCurrentContents);
        } else {
            unlink($this->confPath);
        }        

    }





}



?>
