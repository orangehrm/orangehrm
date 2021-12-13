<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Tests\Core\Dao;

use OrangeHRM\Core\Dao\EmailQueueDao;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Mail;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * EmailQueueDao Test Class
 * @group Core
 * @group Dao
 */
class EmailQueueDaoTest extends KernelTestCase
{
    private EmailQueueDao $emailQueueDao;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->emailQueueDao = new EmailQueueDao();
        $this->fixture = \OrangeHRM\Config\Config::get(\OrangeHRM\Config\Config::PLUGINS_DIR) .
            '/orangehrmCorePlugin/test/fixtures/EmailQueueDao.yml';
        TestDataService::populate($this->fixture);
        $this->createKernel();
    }


    public function testGetEmail(): void
    {
        $result = $this->emailQueueDao->getEmail(1);
        $this->assertEquals('test1 subject', $result->getSubject());
        $this->assertEquals('test1 body', $result->getBody());
    }

    public function testSaveEmail(): void
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $mail = new Mail();
        $mail->setSubject("test7 subject");
        $mail->setBody("test7 body");
        $mail->setToList(['test7@orangehrm.com', 'test8@orangehrm.com']);
        $mail->setCcList(['test9@orangehrm.com']);
        $mail->setBccList(['test10@orangehrm.com']);

        $result = $this->emailQueueDao->saveEmail($mail);
        $this->assertTrue($result instanceof Mail);
        $this->assertEquals("test7 subject", $result->getSubject());
        $this->assertEquals("test7 body", $result->getBody());
        $this->assertEquals(Mail::STATUS_PENDING, $result->getStatus());
        $this->assertEquals(['test7@orangehrm.com', 'test8@orangehrm.com'], $result->getToList());
        $this->assertEquals(['test9@orangehrm.com'], $result->getCcList());
        $this->assertEquals(['test10@orangehrm.com'], $result->getBccList());
    }

    public function testRemoveFromQueue(): void
    {
        $toTobedeletedIds = [3, 2];
        $result = $this->emailQueueDao->removeFromQueue($toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testGetAllPendingMailIds(): void
    {
        $result = $this->emailQueueDao->getAllPendingMailIds();

        $this->assertCount(3, $result);
        $this->assertEquals(1, $result[0]);
        $this->assertEquals(2, $result[1]);
        $this->assertEquals(3, $result[2]);
    }
}
