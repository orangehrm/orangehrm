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

namespace OrangeHRM\Tests\WorkspaceNotifications\Service\Webhook;

use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookDeliveryResult;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Service
 */
class WebhookDeliveryResultTest extends TestCase
{
    public function testSuccessFactoryProducesOkResultWithoutError(): void
    {
        $result = WebhookDeliveryResult::success();

        $this->assertTrue($result->isOk());
        $this->assertNull($result->getErrorMessage());
    }

    public function testFailureFactoryCarriesMessage(): void
    {
        $result = WebhookDeliveryResult::failure('Slack rejected: invalid_token');

        $this->assertFalse($result->isOk());
        $this->assertSame('Slack rejected: invalid_token', $result->getErrorMessage());
    }

    public function testEmptyErrorMessageStillFailureType(): void
    {
        $result = WebhookDeliveryResult::failure('');
        $this->assertFalse($result->isOk());
        $this->assertSame('', $result->getErrorMessage());
    }

    public function testResultIsImmutable(): void
    {
        $ref = new \ReflectionClass(WebhookDeliveryResult::class);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $this->assertStringStartsNotWith(
                'set',
                $method->getName(),
                'WebhookDeliveryResult should remain immutable — no setters'
            );
        }
    }
}
