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

namespace OrangeHRM\Tests\Slack\Service\Webhook;

use OrangeHRM\Slack\Service\Webhook\WebhookDeliveryResult;
use PHPUnit\Framework\TestCase;

/**
 * The orchestrator and the test-webhook API both branch on isOk() / getErrorMessage().
 * If either factory ever produces an ambiguous result (ok=true with an error string,
 * or ok=false with no message) those callers silently misclassify outcomes — hence
 * these tests are tight on the invariants rather than just exercising the API.
 *
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
        // An upstream provider may return a 4xx with an empty body. We still
        // treat it as failure — never as success with empty error.
        $result = WebhookDeliveryResult::failure('');
        $this->assertFalse($result->isOk());
        $this->assertSame('', $result->getErrorMessage());
    }

    public function testResultIsImmutable(): void
    {
        // The DTO has no setters — a quick reflection check guards against
        // someone adding a setter that would let the orchestrator mutate
        // a result after-the-fact (and break the at-a-glance log line).
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
