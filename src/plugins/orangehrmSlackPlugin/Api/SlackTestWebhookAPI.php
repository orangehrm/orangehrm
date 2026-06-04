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
use OrangeHRM\Slack\Service\Webhook\WebhookProviderRegistry;
use OrangeHRM\Slack\Traits\Service\SlackNotificationServiceTrait;
use OrangeHRM\Slack\Traits\Service\SlackRegistrationServiceTrait;
use OrangeHRM\Slack\Traits\Service\WebhookProviderRegistryTrait;

/**
 * "Send Test" endpoint. Two routes land here:
 *   POST /registrations/test          — ad-hoc test for a URL not yet saved
 *   POST /registrations/{id}/test     — test using a saved registration's URL
 *
 * Active-state invariant (decided 2026-06-03):
 *   The test endpoint MUST work regardless of the registration's `is_active`
 *   flag. Admins need to verify a misconfigured row even while it's paused —
 *   in fact that's the most common case for using Test in the first place.
 *   Do NOT add an `if (!$registration->isActive()) throw …` guard to create().
 */
class SlackTestWebhookAPI extends Endpoint implements CollectionEndpoint
{
    use SlackNotificationServiceTrait;
    use SlackRegistrationServiceTrait;
    use WebhookProviderRegistryTrait;

    public const PARAMETER_WEBHOOK_URL = 'webhookUrl';
    public const PARAMETER_EVENT_TYPE = 'eventType';
    public const PARAMETER_PROVIDER = 'provider';

    /**
     * @OA\Post(
     *     path="/api/v2/admin/slack-notification/registrations/test",
     *     tags={"Admin/Slack Notification"},
     *     summary="Send a test Slack notification using a provided webhook URL",
     *     description="Used by the 'Send test' button on the configuration form when entering a brand-new webhook. Posts a polished test message to the URL and reports success/failure.",
     *     operationId="send-slack-test-with-url",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"eventType", "webhookUrl"},
     *             @OA\Property(property="eventType",  type="string", enum={"BIRTHDAY", "LEAVE_TODAY"}),
     *             @OA\Property(property="webhookUrl", type="string", example="https://hooks.slack.com/services/T../B../secret"),
     *             @OA\Property(property="provider",   type="string", enum={"slack", "google_chat"}, example="slack",
     *             description="Webhook provider. Defaults to 'slack' if omitted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Delivered",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status",  type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Test message delivered to Slack.")
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="400", description="Bad request — invalid URL or Slack rejected the delivery")
     * )
     *
     * @OA\Post(
     *     path="/api/v2/admin/slack-notification/registrations/{id}/test",
     *     tags={"Admin/Slack Notification"},
     *     summary="Send a test Slack notification using a saved registration's stored webhook",
     *     description="Used by the per-row 'Send test' action. Decrypts the stored webhook URL and posts the polished test message.",
     *     operationId="send-slack-test-for-registration",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"eventType"},
     *             @OA\Property(property="eventType", type="string", enum={"BIRTHDAY", "LEAVE_TODAY"})
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Delivered",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status",  type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Test message delivered to Slack.")
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="400", description="Registration not found, no stored URL, or Slack rejected the delivery")
     * )
     *
     * @inheritDoc
     */
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

        [$webhookUrl, $providerId] = $this->resolveWebhookAndProvider($registrationId);

        // For saved-row tests, the row is the source of truth — override the
        // body's eventType so a stale form value can't cause us to render the
        // wrong template (e.g. Birthday body for a LEAVE_TODAY row).
        if ($registrationId !== null && $registrationId > 0) {
            $registration = $this->getSlackRegistrationService()->getRegistration($registrationId);
            if ($registration instanceof SlackRegistration) {
                $eventType = $registration->getEventType();
            }
        }

        $registry = $this->getWebhookProviderRegistry();
        if (!$registry->has($providerId)) {
            throw new BadRequestException('Unsupported webhook provider.');
        }
        if (!$registry->get($providerId)->validateUrl($webhookUrl)) {
            throw new BadRequestException('Invalid Slack incoming webhook URL.');
        }

        $result = $this->getSlackNotificationService()->sendTestMessage($webhookUrl, $eventType, $providerId);
        if (!$result->isOk()) {
            throw new BadRequestException($result->getErrorMessage() ?? 'Failed to deliver to Slack.');
        }

        return new EndpointResourceResult(ArrayModel::class, [
            'status' => 'success',
            'message' => 'Test message delivered to Slack.',
        ]);
    }

    /**
     * Returns [webhookUrl, providerId]. For a brand-new URL typed into the
     * form, the provider is taken from the body `provider` field (defaults to
     * 'slack' if omitted — covers older clients that don't know about the
     * multi-provider seam). For a saved registration, the provider is read
     * from the row so tests route through the right transport regardless of
     * what the client posts.
     *
     * @return array{0:string, 1:string}
     */
    private function resolveWebhookAndProvider(?int $registrationId): array
    {
        $bodyUrl = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_WEBHOOK_URL
        );
        if ($bodyUrl !== null && $bodyUrl !== '') {
            $bodyProvider = $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PROVIDER
            );
            return [$bodyUrl, $bodyProvider ?: SlackRegistration::PROVIDER_SLACK];
        }

        if ($registrationId === null || $registrationId <= 0) {
            throw new BadRequestException('webhookUrl is required for new registrations.');
        }

        $registration = $this->getSlackRegistrationService()->getRegistration($registrationId);
        if (!$registration instanceof SlackRegistration) {
            throw new BadRequestException('Registration not found.');
        }
        $stored = $this->getSlackRegistrationService()->decryptWebhookUrl($registration);
        if ($stored === null || $stored === '') {
            throw new BadRequestException('Registration has no stored webhook URL.');
        }
        return [$stored, $registration->getProvider()];
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
                    new Rule(Rules::LENGTH, [null, WebhookProviderRegistry::MAX_URL_LENGTH])
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PROVIDER,
                    new Rule(Rules::IN, [[
                        SlackRegistration::PROVIDER_SLACK,
                        SlackRegistration::PROVIDER_GOOGLE_CHAT,
                    ]])
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
