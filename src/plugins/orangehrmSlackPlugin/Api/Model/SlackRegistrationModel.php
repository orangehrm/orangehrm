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

namespace OrangeHRM\Slack\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Service\SlackRegistrationService;
use OrangeHRM\Slack\Traits\Service\SlackRegistrationServiceTrait;

/**
 * @OA\Schema(
 *     schema="Slack-RegistrationModel",
 *     type="object",
 *     description="One configured Slack notification destination.",
 *     @OA\Property(property="id",            type="integer", example=1),
 *     @OA\Property(property="provider",      type="string",  example="slack"),
 *     @OA\Property(property="eventType",     type="string",  example="BIRTHDAY",
 *     description="One of the values returned by SlackConfigModel.eventTypes."),
 *     @OA\Property(property="webhookUrl",    type="string",  nullable=true,
 *         description="Masked URL — only the workspace + channel-id segments are exposed; the secret is replaced with `…`. Null when no webhook is stored.",
 *     example="https://hooks.slack.com/services/TH6MG0V7D/B0AU672L2LA/…"),
 *     @OA\Property(property="channelLabel",  type="string",  nullable=true, example="#hr-team"),
 *     @OA\Property(property="subunits",      type="array",
 *         description="Multi-subunit filter. Empty array means 'all employees, no filter'.",
 *         @OA\Items(type="object",
 *             @OA\Property(property="id",   type="integer", example=4),
 *             @OA\Property(property="name", type="string",  example="Engineering")
 *         )
 *     ),
 *     @OA\Property(property="timezone",      type="string",  example="Asia/Colombo"),
 *     @OA\Property(property="dailySendTime", type="string",  example="09:00"),
 *     @OA\Property(property="active",        type="boolean", example=true)
 * )
 *
 * Webhook URL is masked on read — only the leading channel-identifier segments are returned,
 * and only when one has actually been stored. The encrypted ciphertext never leaves the API.
 *
 * The {@see SlackRegistrationService} dependency is fetched from the DI container via the
 * trait, so this model can be instantiated once per row in a `GET /registrations` collection
 * response without paying a `new` cost on every row.
 */
class SlackRegistrationModel implements Normalizable
{
    use SlackRegistrationServiceTrait;

    private SlackRegistration $registration;

    public function __construct(SlackRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function toArray(): array
    {
        $maskedWebhook = null;
        if ($this->registration->getWebhookUrl() !== '') {
            $service = $this->getSlackRegistrationService();
            $plain = $service->decryptWebhookUrl($this->registration);
            // Route via the registration's own provider id so the mask shape
            // matches the FE mirror's masker for the same URL. Cross-provider
            // drift here = duplicate-detection false negatives on the FE.
            $maskedWebhook = SlackRegistrationService::maskWebhookUrl(
                $plain,
                $this->registration->getProvider()
            );
        }

        $subunits = [];
        foreach ($this->registration->getSubunits() as $subunit) {
            $subunits[] = [
                'id' => $subunit->getId(),
                'name' => $subunit->getName(),
            ];
        }

        return [
            'id' => $this->registration->getId(),
            'provider' => $this->registration->getProvider(),
            'eventType' => $this->registration->getEventType(),
            'webhookUrl' => $maskedWebhook,
            'channelLabel' => $this->registration->getChannelLabel(),
            'subunits' => $subunits,
            'timezone' => $this->registration->getTimezone(),
            'dailySendTime' => $this->registration->getDailySendTime(),
            'active' => $this->registration->isActive(),
        ];
    }
}
