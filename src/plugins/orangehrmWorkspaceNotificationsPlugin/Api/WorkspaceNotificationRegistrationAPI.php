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

namespace OrangeHRM\WorkspaceNotifications\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\WorkspaceNotifications\Api\Model\WorkspaceNotificationRegistrationModel;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookProviderRegistry;
use OrangeHRM\WorkspaceNotifications\Traits\Service\WorkspaceNotificationRegistrationServiceTrait;
use OrangeHRM\WorkspaceNotifications\Traits\Service\WebhookProviderRegistryTrait;

class WorkspaceNotificationRegistrationAPI extends Endpoint implements CrudEndpoint
{
    use WorkspaceNotificationRegistrationServiceTrait;
    use WebhookProviderRegistryTrait;

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

    /**
     * @OA\Get(
     *     path="/api/v2/admin/workspace-notification/registrations",
     *     tags={"Admin/Workspace Notification"},
     *     summary="List Workspace notification registrations",
     *     operationId="list-workspace-notification-registrations",
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Slack-RegistrationModel")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=2)
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $registrations = $this->getWorkspaceNotificationRegistrationService()->listRegistrations();
        return new EndpointCollectionResult(
            WorkspaceNotificationRegistrationModel::class,
            $registrations,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => count($registrations)])
        );
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/workspace-notification/registrations/{id}",
     *     tags={"Admin/Workspace Notification"},
     *     summary="Get one Workspace notification registration",
     *     operationId="get-workspace-notification-registration",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Slack-RegistrationModel"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $registration = $this->getWorkspaceNotificationRegistrationService()->getRegistration($id);
        $this->throwIfMissing($registration);
        return new EndpointResourceResult(WorkspaceNotificationRegistrationModel::class, $registration);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/workspace-notification/registrations",
     *     tags={"Admin/Workspace Notification"},
     *     summary="Create a Workspace notification registration",
     *     operationId="create-workspace-notification-registration",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"eventType", "webhookUrl", "timezone", "dailySendTime"},
     *             @OA\Property(property="eventType",     type="string", enum={"BIRTHDAY", "LEAVE_TODAY"}),
     *             @OA\Property(property="provider",      type="string", enum={"slack", "google_chat"}, example="slack",
     *             description="Webhook provider. Defaults to 'slack' if omitted. URL shape is validated against the selected provider."),
     *             @OA\Property(property="webhookUrl",    type="string", example="https://hooks.slack.com/services/T../B../secret"),
     *             @OA\Property(property="channelLabel",  type="string", nullable=true, example="#hr-team"),
     *             @OA\Property(property="subunitIds",    type="array", @OA\Items(type="integer"), description="Empty/omitted = all employees."),
     *             @OA\Property(property="timezone",      type="string", example="Asia/Colombo"),
     *             @OA\Property(property="dailySendTime", type="string", example="09:00", description="HH:mm in the registration's timezone."),
     *             @OA\Property(property="active",        type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Created",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Slack-RegistrationModel"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $payload = $this->readPayload();
        $this->assertWebhookUrlMatchesProvider(
            $payload['provider'] ?? WorkspaceNotificationRegistration::PROVIDER_SLACK,
            $payload['webhookUrl'] ?? ''
        );

        $registration = $this->getWorkspaceNotificationRegistrationService()->createRegistration($payload);
        return new EndpointResourceResult(WorkspaceNotificationRegistrationModel::class, $registration);
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return $this->getBodyRules(true);
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/workspace-notification/registrations/{id}",
     *     tags={"Admin/Workspace Notification"},
     *     summary="Update a Workspace notification registration (partial update supported)",
     *     description="All body fields are optional. A PUT containing only `{""active"": true}` toggles the row's active flag without touching anything else. Omitting `webhookUrl` keeps the stored encrypted value.",
     *     operationId="update-workspace-notification-registration",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="eventType",     type="string", enum={"BIRTHDAY", "LEAVE_TODAY"}),
     *             @OA\Property(property="provider",      type="string", enum={"slack", "google_chat"}),
     *             @OA\Property(property="webhookUrl",    type="string", description="Omit to keep the stored value. Validated against the effective provider (payload provider, or stored row provider)."),
     *             @OA\Property(property="channelLabel",  type="string", nullable=true),
     *             @OA\Property(property="subunitIds",    type="array",  @OA\Items(type="integer")),
     *             @OA\Property(property="timezone",      type="string"),
     *             @OA\Property(property="dailySendTime", type="string", example="09:00"),
     *             @OA\Property(property="active",        type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Slack-RegistrationModel"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $registration = $this->getWorkspaceNotificationRegistrationService()->getRegistration($id);
        $this->throwIfMissing($registration);

        $payload = $this->readPayload();
        if (isset($payload['webhookUrl'])) {
            $effectiveProvider = $payload['provider'] ?? $registration->getProvider();
            $this->assertWebhookUrlMatchesProvider($effectiveProvider, $payload['webhookUrl']);
        }

        $updated = $this->getWorkspaceNotificationRegistrationService()->updateRegistration(
            $registration,
            $payload
        );
        return new EndpointResourceResult(WorkspaceNotificationRegistrationModel::class, $updated);
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return $this->getBodyRules(false);
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/workspace-notification/registrations",
     *     tags={"Admin/Workspace Notification"},
     *     summary="Delete one or more Workspace notification registrations",
     *     description="Bulk delete by ids. Cascades to ohrm_workspace_notification_log entries.",
     *     operationId="delete-workspace-notification-registrations",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"ids"},
     *             @OA\Property(property="ids", type="array", @OA\Items(type="integer"), example={1, 3})
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="integer")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_IDS
        );
        foreach ($ids as $id) {
            $registration = $this->getWorkspaceNotificationRegistrationService()->getRegistration((int)$id);
            if ($registration instanceof WorkspaceNotificationRegistration) {
                $this->getWorkspaceNotificationRegistrationService()->getDao()->deleteRegistration($registration);
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

    private function getBodyRules(bool $forCreate): ParamRuleCollection
    {
        $eventTypeRule = new ParamRule(
            self::PARAMETER_EVENT_TYPE,
            new Rule(Rules::IN, [WorkspaceNotificationRegistration::EVENT_TYPES])
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
            new Rule(Rules::LENGTH, [null, WebhookProviderRegistry::MAX_URL_LENGTH])
        );
        $providerRule = new ParamRule(
            self::PARAMETER_PROVIDER,
            new Rule(Rules::IN, [[
                WorkspaceNotificationRegistration::PROVIDER_SLACK,
                WorkspaceNotificationRegistration::PROVIDER_GOOGLE_CHAT,
                WorkspaceNotificationRegistration::PROVIDER_TEAMS,
            ]])
        );
        $channelLabelRule = new ParamRule(
            self::PARAMETER_CHANNEL_LABEL,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CHANNEL_LABEL_MAX])
        );

        $rules = [];
        if ($forCreate) {
            $rules[] = $eventTypeRule;
            $rules[] = $webhookRule;
            $rules[] = $timezoneRule;
            $rules[] = $sendTimeRule;
        } else {
            $decorator = $this->getValidationDecorator();
            $rules[] = $decorator->notRequiredParamRule($eventTypeRule, true);
            $rules[] = $decorator->notRequiredParamRule($webhookRule, true);
            $rules[] = $decorator->notRequiredParamRule($timezoneRule, true);
            $rules[] = $decorator->notRequiredParamRule($sendTimeRule, true);
            $rules[] = new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE));
        }

        $rules[] = $this->getValidationDecorator()->notRequiredParamRule($providerRule, true);
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
        if (!$registration instanceof WorkspaceNotificationRegistration) {
            throw new RecordNotFoundException();
        }
    }

    private function assertWebhookUrlMatchesProvider(string $providerId, string $url): void
    {
        $registry = $this->getWebhookProviderRegistry();
        if (!$registry->has($providerId)) {
            throw new BadRequestException("Unsupported webhook provider: {$providerId}");
        }
        if (!$registry->get($providerId)->validateUrl($url)) {
            throw new BadRequestException(
                "Invalid webhook URL for provider '{$providerId}'."
            );
        }
    }
}
