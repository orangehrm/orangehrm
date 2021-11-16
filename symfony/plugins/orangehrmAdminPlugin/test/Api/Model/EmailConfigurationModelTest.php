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

namespace OrangeHRM\Tests\Admin\Api\Model;

use OrangeHRM\Admin\Api\Model\EmailConfigurationModel;
use OrangeHRM\Entity\EmailConfiguration;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Admin
 * @group Model
 */
class EmailConfigurationModelTest extends KernelTestCase
{
    public function testToArray()
    {
        $resultArray = [
            "mailType" => "smtp",
            "sentAs" => "test@orangehrm.com",
            "smtpHost" => "smtp.gmail.com",
            "smtpPort" => 587,
            "smtpUsername" => "testUN",
            "smtpAuthType" => "login",
            "smtpSecurityType" => "tls"
        ];

        $emailConfiguration = new EmailConfiguration();
        $emailConfiguration->setId(1);
        $emailConfiguration->setMailType("smtp");
        $emailConfiguration->setSentAs("test@orangehrm.com");
        $emailConfiguration->setSmtpHost("smtp.gmail.com");
        $emailConfiguration->setSmtpPort(587);
        $emailConfiguration->setSmtpUsername("testUN");
        $emailConfiguration->setSmtpPassword("testPW");
        $emailConfiguration->setSmtpAuthType("login");
        $emailConfiguration->setSmtpSecurityType("tls");

        $emailConfigurationModel = new EmailConfigurationModel($emailConfiguration);

        $this->assertEquals($resultArray, $emailConfigurationModel->toArray());
    }
}
