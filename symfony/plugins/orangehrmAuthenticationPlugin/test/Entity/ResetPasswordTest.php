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

use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\ResetPasswordRequest;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Authentication
 * @group Entity
 */
class ResetPasswordTest extends EntityTestCase
{
    use DateTimeHelperTrait;

    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([ResetPasswordRequest::class]);
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
    }

    public function testResetPassword(): void
    {
        $resetPassword = new ResetPasswordRequest();
        $resetPassword->setId(1);
        $resetPassword->setResetCode('reset');
        $resetPassword->setResetRequestDate($this->getDateTimeHelper()->getNow());
        $resetPassword->setResetEmail('reset@gmail.com');
        $this->persist($resetPassword);

        /** @var ResetPasswordRequest $resetPassword */
        $resetPassword = $this->getRepository(ResetPasswordRequest::class)->find(1);
        $this->assertEquals(1, $resetPassword->getId());
        $this->assertEquals('reset', $resetPassword->getResetCode());
        $this->assertEquals('reset@gmail.com', $resetPassword->getResetEmail());
    }
}
