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
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Slack\Api\Model\SlackConfigModel;
use OrangeHRM\Slack\Service\SlackRegistrationService;
use OrangeHRM\Slack\Service\SlackSettingsService;
use OrangeHRM\Slack\Service\Webhook\SlackWebhookClient;

class SlackConfigAPI extends Endpoint implements ResourceEndpoint
{
    public const PARAMETER_ENABLE = 'enable';
    public const PARAMETER_TIMEZONE = 'timezone';
    public const PARAMETER_DAILY_SEND_TIME = 'dailySendTime';
    public const PARAMETER_REGISTRATIONS = 'registrations';

    public const PARAM_RULE_TIMEZONE_MAX_LENGTH = 64;
    public const PARAM_RULE_SEND_TIME_MAX_LENGTH = 5;
    public const PARAM_RULE_CHANNEL_LABEL_MAX_LENGTH = 100;

    private ?SlackSettingsService $settingsService = null;
    private ?SlackRegistrationService $registrationService = null;

    public function getSettingsService(): SlackSettingsService
    {
        if ($this->settingsService === null) {
            $this->settingsService = new SlackSettingsService();
        }
        return $this->settingsService;
    }

    public function getRegistrationService(): SlackRegistrationService
    {
        if ($this->registrationService === null) {
            $this->registrationService = new SlackRegistrationService();
        }
        return $this->registrationService;
    }

    public function getOne(): EndpointResourceResult
    {
        $settings = $this->getSettingsService()->getSettings();
        return new EndpointResourceResult(SlackConfigModel::class, $settings);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
        );
    }

    public function update(): EndpointResourceResult
    {
        $enable = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ENABLE
        );
        $timezone = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_TIMEZONE
        );
        $sendTime = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DAILY_SEND_TIME
        );
        $registrations = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_REGISTRATIONS,
            []
        );

        $settings = $this->getSettingsService()->updateSettings($enable, $timezone, $sendTime);
        $this->getRegistrationService()->syncFromPayload($registrations);

        return new EndpointResourceResult(SlackConfigModel::class, $settings);
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
            new ParamRule(self::PARAMETER_ENABLE, new Rule(Rules::BOOL_TYPE)),
            new ParamRule(
                self::PARAMETER_TIMEZONE,
                new Rule(Rules::REQUIRED),
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TIMEZONE_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_DAILY_SEND_TIME,
                new Rule(Rules::REQUIRED),
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SEND_TIME_MAX_LENGTH]),
                new Rule(Rules::REGEX, ['/^([01]\d|2[0-3]):[0-5]\d$/']),
            ),
            new ParamRule(self::PARAMETER_REGISTRATIONS, new Rule(Rules::ARRAY_TYPE)),
        );
    }

    public function delete(): EndpointResourceResult
    {
        throw new NotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }
}
