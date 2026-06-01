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

namespace OrangeHRM\Slack\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Service\SlackNotificationService;
use OrangeHRM\Slack\Service\SlackRegistrationService;
use OrangeHRM\Slack\Service\Webhook\SlackWebhookClient;

class SlackTestWebhookAPI extends Endpoint implements CollectionEndpoint
{
    public const PARAMETER_WEBHOOK_URL = 'webhookUrl';
    public const PARAMETER_EVENT_TYPE = 'eventType';

    private ?SlackNotificationService $notificationService = null;
    private ?SlackRegistrationService $registrationService = null;

    public function getNotificationService(): SlackNotificationService
    {
        if ($this->notificationService === null) {
            $this->notificationService = new SlackNotificationService();
        }
        return $this->notificationService;
    }

    public function getRegistrationService(): SlackRegistrationService
    {
        if ($this->registrationService === null) {
            $this->registrationService = new SlackRegistrationService();
        }
        return $this->registrationService;
    }

    public function create(): EndpointResult
    {
        $eventType = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EVENT_TYPE
        );

        $registrationId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );

        $webhookUrl = $this->resolveWebhookUrl($registrationId);
        if (!SlackWebhookClient::isValidWebhookUrl($webhookUrl)) {
            throw new BadRequestException('Invalid Slack incoming webhook URL.');
        }

        $result = $this->getNotificationService()->sendTestMessage($webhookUrl, $eventType);
        if (!$result->isOk()) {
            throw new BadRequestException($result->getErrorMessage() ?? 'Failed to deliver to Slack.');
        }

        return new EndpointResourceResult(ArrayModel::class, [
            'status' => 'success',
            'message' => 'Test message delivered to Slack.',
        ]);
    }

    private function resolveWebhookUrl(?int $registrationId): string
    {
        $bodyUrl = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_WEBHOOK_URL
        );
        if ($bodyUrl !== null && $bodyUrl !== '') {
            return $bodyUrl;
        }

        if ($registrationId === null || $registrationId <= 0) {
            throw new BadRequestException('webhookUrl is required for new registrations.');
        }

        $registration = $this->getRegistrationService()->getRegistration($registrationId);
        if (!$registration instanceof SlackRegistration) {
            throw new BadRequestException('Registration not found.');
        }
        $stored = $this->getRegistrationService()->decryptWebhookUrl($registration);
        if ($stored === null || $stored === '') {
            throw new BadRequestException('Registration has no stored webhook URL.');
        }
        return $stored;
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
            new ParamRule(
                self::PARAMETER_EVENT_TYPE,
                new Rule(Rules::REQUIRED),
                new Rule(Rules::IN, [SlackRegistration::EVENT_TYPES])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_WEBHOOK_URL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, SlackWebhookClient::WEBHOOK_URL_MAX_LENGTH])
                ),
                true
            ),
        );
    }

    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
