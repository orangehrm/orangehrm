<?php

use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

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

class ResetPasswordServiceTest extends KernelTestCase
{
    private string $fixture;
    private ResetPasswordService $resetPasswordService;


    protected function setUp(): void
    {
        $this->resetPasswordService = new ResetPasswordService();
        $this->fixture = Config::get(
                Config::PLUGINS_DIR
            ) . '/orangehrmMaintenancePlugin/test/fixtures/EmployeeMaintenence.yml';
        TestDataService::populate($this->fixture);

    }


    public function testGetEmailService():void{
        $emailService=$this->resetPasswordService->getEmailService();
        $this->assertInstanceOf(EmailService::class,$emailService);
    }

}
