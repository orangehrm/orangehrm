<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Authentication\Entity;

use DateTime;
use OrangeHRM\Core\Service\DateTimeHelperService;
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
        $resetPassword->setResetRequestDate(new DateTime('2022-07-04 10:56:56'));
        $resetPassword->setResetEmail('reset@gmail.com');
        $resetPassword->setExpired(1);
        $this->persist($resetPassword);

        /** @var ResetPasswordRequest $resetPassword */
        $resetPassword = $this->getRepository(ResetPasswordRequest::class)->find(1);
        $this->assertEquals(1, $resetPassword->getId());
        $this->assertEquals('reset', $resetPassword->getResetCode());
        $this->assertEquals('reset@gmail.com', $resetPassword->getResetEmail());
        $this->assertEquals(1, $resetPassword->getExpired());
        $this->assertEquals('2022-07-04 10:56', $resetPassword->getResetRequestDate()->format('Y-m-d H:i'));
    }
}
