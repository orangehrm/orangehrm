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

/**
 * Webhook URL is masked on read — only the leading channel-identifier segments are returned,
 * and only when one has actually been stored. The encrypted ciphertext never leaves the API.
 *
 * Subunits are returned as an array (multi-subunit filter); empty array means "all employees".
 */
class SlackRegistrationModel implements Normalizable
{
    private SlackRegistration $registration;
    private ?SlackRegistrationService $registrationService;

    public function __construct(SlackRegistration $registration, ?SlackRegistrationService $registrationService = null)
    {
        $this->registration = $registration;
        $this->registrationService = $registrationService;
    }

    public function toArray(): array
    {
        $maskedWebhook = null;
        if ($this->registration->getWebhookUrl() !== '') {
            $service = $this->registrationService ?? new SlackRegistrationService();
            $plain = $service->decryptWebhookUrl($this->registration);
            $maskedWebhook = SlackRegistrationService::maskWebhookUrl($plain);
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
