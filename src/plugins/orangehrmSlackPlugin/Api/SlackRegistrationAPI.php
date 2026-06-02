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
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Api\Model\SlackRegistrationModel;
use OrangeHRM\Slack\Service\SlackRegistrationService;
use OrangeHRM\Slack\Service\Webhook\SlackWebhookClient;

class SlackRegistrationAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_EVENT_TYPE = 'eventType';
    public const PARAMETER_WEBHOOK_URL = 'webhookUrl';
    public const PARAMETER_CHANNEL_LABEL = 'channelLabel';
    public const PARAMETER_SUBUNIT_IDS = 'subunitIds';
    public const PARAMETER_TIMEZONE = 'timezone';
    public const PARAMETER_DAILY_SEND_TIME = 'dailySendTime';
    public const PARAMETER_ACTIVE = 'active';
    public const PARAMETER_PROVIDER = 'provider';

    public const PARAM_RULE_CHANNEL_LABEL_MAX = 100;
    public const PARAM_RULE_TIMEZONE_MAX = 64;
    public const PARAM_RULE_SEND_TIME_MAX = 5;

    private ?SlackRegistrationService $registrationService = null;

    public function getRegistrationService(): SlackRegistrationService
    {
        if ($this->registrationService === null) {
            $this->registrationService = new SlackRegistrationService();
        }
        return $this->registrationService;
    }

    public function getAll(): EndpointCollectionResult
    {
        $registrations = $this->getRegistrationService()->listRegistrations();
        return new EndpointCollectionResult(
            SlackRegistrationModel::class,
            $registrations,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => count($registrations)])
        );
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
    }

    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $registration = $this->getRegistrationService()->getRegistration($id);
        $this->throwIfMissing($registration);
        return new EndpointResourceResult(SlackRegistrationModel::class, $registration);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
        );
    }

    public function create(): EndpointResult
    {
        $registration = $this->getRegistrationService()->createRegistration(
            $this->readPayload()
        );
        return new EndpointResourceResult(SlackRegistrationModel::class, $registration);
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return $this->getBodyRules(true);
    }

    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $registration = $this->getRegistrationService()->getRegistration($id);
        $this->throwIfMissing($registration);

        $updated = $this->getRegistrationService()->updateRegistration(
            $registration,
            $this->readPayload()
        );
        return new EndpointResourceResult(SlackRegistrationModel::class, $updated);
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return $this->getBodyRules(false);
    }

    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_IDS
        );
        foreach ($ids as $id) {
            $registration = $this->getRegistrationService()->getRegistration((int)$id);
            if ($registration instanceof SlackRegistration) {
                $this->getRegistrationService()->getDao()->deleteRegistration($registration);
            }
        }
        return new EndpointResourceResult(\OrangeHRM\Core\Api\V2\Model\ArrayModel::class, $ids);
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS, new Rule(Rules::ARRAY_TYPE)),
        );
    }

    /**
     * Build a sparse payload — only fields that are *present in the body* end up in the array.
     * That gives the service-layer partial-update semantics (a PUT with just {"active": true}
     * preserves all other fields).
     *
     * `*OrNull()` returns null only when the field is absent from the body; an explicit
     * empty-string in the body comes through as `''` and is treated as "set to empty".
     *
     * @return array<string,mixed>
     */
    private function readPayload(): array
    {
        $p = $this->getRequestParams();
        $payload = [];

        $eventType = $p->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EVENT_TYPE);
        if ($eventType !== null) {
            $payload['eventType'] = $eventType;
        }
        $provider = $p->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PROVIDER);
        if ($provider !== null) {
            $payload['provider'] = $provider;
        }
        $webhook = $p->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_WEBHOOK_URL);
        if ($webhook !== null) {
            $payload['webhookUrl'] = $webhook;
        }
        $channelLabel = $p->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CHANNEL_LABEL);
        if ($channelLabel !== null) {
            $payload['channelLabel'] = $channelLabel;
        }
        $subunitIds = $p->getArrayOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_SUBUNIT_IDS);
        if ($subunitIds !== null) {
            $payload['subunitIds'] = $subunitIds;
        }
        $timezone = $p->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TIMEZONE);
        if ($timezone !== null) {
            $payload['timezone'] = $timezone;
        }
        $sendTime = $p->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DAILY_SEND_TIME);
        if ($sendTime !== null) {
            $payload['dailySendTime'] = $sendTime;
        }
        $active = $p->getBooleanOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ACTIVE);
        if ($active !== null) {
            $payload['active'] = $active;
        }
        return $payload;
    }

    /**
     * Validation shared between create and update.
     *  - On create: eventType + webhookUrl + timezone + dailySendTime are required.
     *  - On update: ALL fields optional — supports partial PUTs (e.g. just `{"active": true}`).
     *    Format constraints still apply when a field is present (HH:mm regex, Slack URL regex, etc.).
     */
    private function getBodyRules(bool $forCreate): ParamRuleCollection
    {
        $eventTypeRule = new ParamRule(
            self::PARAMETER_EVENT_TYPE,
            new Rule(Rules::IN, [SlackRegistration::EVENT_TYPES])
        );
        $timezoneRule = new ParamRule(
            self::PARAMETER_TIMEZONE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TIMEZONE_MAX])
        );
        $sendTimeRule = new ParamRule(
            self::PARAMETER_DAILY_SEND_TIME,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SEND_TIME_MAX]),
            new Rule(Rules::REGEX, ['/^([01]\d|2[0-3]):[0-5]\d$/'])
        );
        $webhookRule = new ParamRule(
            self::PARAMETER_WEBHOOK_URL,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, SlackWebhookClient::WEBHOOK_URL_MAX_LENGTH]),
            new Rule(Rules::REGEX, ['#^https://hooks\.slack\.com/services/[A-Z0-9]+/[A-Z0-9]+/[A-Za-z0-9]+$#'])
        );
        $channelLabelRule = new ParamRule(
            self::PARAMETER_CHANNEL_LABEL,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CHANNEL_LABEL_MAX])
        );

        $rules = [];
        if ($forCreate) {
            // Required-on-create: eventType, webhookUrl, timezone, dailySendTime.
            $rules[] = $eventTypeRule;
            $rules[] = $webhookRule;
            $rules[] = $timezoneRule;
            $rules[] = $sendTimeRule;
        } else {
            // Update: every field optional. Format constraints apply only when present.
            $decorator = $this->getValidationDecorator();
            $rules[] = $decorator->notRequiredParamRule($eventTypeRule, true);
            $rules[] = $decorator->notRequiredParamRule($webhookRule, true);
            $rules[] = $decorator->notRequiredParamRule($timezoneRule, true);
            $rules[] = $decorator->notRequiredParamRule($sendTimeRule, true);
            $rules[] = new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE));
        }

        // Always-optional fields (same shape on create and update).
        $rules[] = $this->getValidationDecorator()->notRequiredParamRule($channelLabelRule, true);
        $rules[] = $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(self::PARAMETER_SUBUNIT_IDS, new Rule(Rules::ARRAY_TYPE)),
            true
        );
        $rules[] = $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(self::PARAMETER_ACTIVE, new Rule(Rules::BOOL_TYPE)),
            true
        );

        return new ParamRuleCollection(...$rules);
    }

    /**
     * @param mixed $registration
     */
    private function throwIfMissing($registration): void
    {
        if (!$registration instanceof SlackRegistration) {
            throw new RecordNotFoundException();
        }
    }
}
