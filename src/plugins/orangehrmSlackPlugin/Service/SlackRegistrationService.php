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

namespace OrangeHRM\Slack\Service;

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Utility\EncryptionHelperTrait;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\Slack\Dao\SlackRegistrationDao;

class SlackRegistrationService
{
    use EncryptionHelperTrait;
    use EntityManagerHelperTrait;

    private ?SlackRegistrationDao $dao = null;

    public function getDao(): SlackRegistrationDao
    {
        if ($this->dao === null) {
            $this->dao = new SlackRegistrationDao();
        }
        return $this->dao;
    }

    /**
     * @return SlackRegistration[]
     */
    public function listRegistrations(): array
    {
        return $this->getDao()->listRegistrations();
    }

    /**
     * @return SlackRegistration[]
     */
    public function listActiveRegistrations(): array
    {
        return $this->getDao()->listActiveRegistrations();
    }

    public function getRegistration(int $id): ?SlackRegistration
    {
        return $this->getDao()->getRegistration($id);
    }

    public function decryptWebhookUrl(SlackRegistration $registration): ?string
    {
        return self::encryptionEnabled()
            ? self::getCryptographer()->decrypt($registration->getWebhookUrl())
            : $registration->getWebhookUrl();
    }

    /**
     * Create a single registration from the API payload. Webhook URL is required here
     * (validated at the API layer) and gets encrypted before persistence.
     *
     * @param array<string,mixed> $row
     */
    public function createRegistration(array $row): SlackRegistration
    {
        $registration = new SlackRegistration();
        $registration->setCreatedAt(new DateTime());
        $this->applyPayload($registration, $row, true);
        return $this->getDao()->saveRegistration($registration);
    }

    /**
     * Update an existing registration in-place. webhookUrl may be omitted to keep the
     * stored encrypted value untouched.
     *
     * @param array<string,mixed> $row
     */
    public function updateRegistration(SlackRegistration $registration, array $row): SlackRegistration
    {
        $this->applyPayload($registration, $row, false);
        return $this->getDao()->saveRegistration($registration);
    }

    /**
     * Sparse payload semantics — only keys that are *present* in $row touch the entity.
     * A PUT body of just `{"active": false}` flips the active flag and leaves the rest alone.
     *
     * @param array<string,mixed> $row
     */
    private function applyPayload(SlackRegistration $registration, array $row, bool $isNew): void
    {
        if (array_key_exists('eventType', $row) && $row['eventType'] !== '') {
            $registration->setEventType((string)$row['eventType']);
        } elseif ($isNew) {
            throw new \InvalidArgumentException('eventType is required for new registrations.');
        }

        if (array_key_exists('provider', $row) && $row['provider'] !== '') {
            $registration->setProvider((string)$row['provider']);
        }

        if (array_key_exists('channelLabel', $row)) {
            $registration->setChannelLabel($row['channelLabel'] !== '' ? (string)$row['channelLabel'] : null);
        }

        if (array_key_exists('active', $row)) {
            $registration->setActive((bool)$row['active']);
        } elseif ($isNew) {
            $registration->setActive(true);
        }

        if (array_key_exists('timezone', $row) && $row['timezone'] !== '') {
            $registration->setTimezone((string)$row['timezone']);
        }

        if (array_key_exists('dailySendTime', $row) && $row['dailySendTime'] !== '') {
            $registration->setDailySendTime((string)$row['dailySendTime']);
        }

        // Multi-subunit: only replace the collection if the field was actually sent.
        // An empty array IS meaningful — clears all subunit filters (= all employees).
        if (array_key_exists('subunitIds', $row) && is_array($row['subunitIds'])) {
            $this->syncSubunits($registration, $row['subunitIds']);
        }

        $newWebhook = array_key_exists('webhookUrl', $row) ? $row['webhookUrl'] : null;
        if ($newWebhook !== null && $newWebhook !== '') {
            $registration->setWebhookUrl($this->encryptForStorage((string)$newWebhook));
        } elseif ($isNew) {
            throw new \InvalidArgumentException('Webhook URL is required for new registrations.');
        }

        $registration->setUpdatedAt(new DateTime());
    }

    /**
     * Resolve the given subunit IDs to entities and set them as the registration's filter.
     *
     * @param int[]|string[] $subunitIds
     */
    private function syncSubunits(SlackRegistration $registration, array $subunitIds): void
    {
        $registration->clearSubunits();
        foreach ($subunitIds as $id) {
            if ((int)$id <= 0) {
                continue;
            }
            /** @var Subunit|null $subunit */
            $subunit = $this->getRepository(Subunit::class)->find((int)$id);
            if ($subunit instanceof Subunit) {
                $registration->addSubunit($subunit);
            }
        }
    }

    public function encryptForStorage(string $plain): string
    {
        return self::encryptionEnabled()
            ? self::getCryptographer()->encrypt($plain)
            : $plain;
    }

    /**
     * Mask a webhook URL for display. The shape of the secret-bearing portion
     * is provider-specific (path segment for Slack, query string for Google
     * Chat, …) so each provider implements its own {@see WebhookProviderInterface::maskUrl()}.
     *
     * Pass the registration's `provider` column as $providerId when one is
     * available (the normal list-read path). For callers that don't know the
     * provider — e.g. test-webhook with a just-typed URL not yet persisted —
     * the dispatcher tries every registered provider until one's `validateUrl`
     * accepts the input, then masks via that provider. If nothing matches
     * we fall back to the safe "drop the last path segment" rule.
     */
    public static function maskWebhookUrl(?string $url, ?string $providerId = null): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }
        $registry = new \OrangeHRM\Slack\Service\Webhook\WebhookProviderRegistry();
        if ($providerId !== null && $registry->has($providerId)) {
            return $registry->get($providerId)->maskUrl($url);
        }
        // Provider not specified — find one that recognises the URL shape.
        foreach ($registry->all() as $provider) {
            if ($provider->validateUrl($url)) {
                return $provider->maskUrl($url);
            }
        }
        return \OrangeHRM\Slack\Service\Webhook\SlackWebhookProvider::genericMask($url);
    }
}
