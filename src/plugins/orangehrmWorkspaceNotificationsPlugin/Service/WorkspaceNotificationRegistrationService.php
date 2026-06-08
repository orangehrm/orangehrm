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

namespace OrangeHRM\WorkspaceNotifications\Service;

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Utility\EncryptionHelperTrait;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\WorkspaceNotifications\Dao\WorkspaceNotificationRegistrationDao;

class WorkspaceNotificationRegistrationService
{
    use EncryptionHelperTrait;
    use EntityManagerHelperTrait;

    private ?WorkspaceNotificationRegistrationDao $dao = null;

    public function getDao(): WorkspaceNotificationRegistrationDao
    {
        if ($this->dao === null) {
            $this->dao = new WorkspaceNotificationRegistrationDao();
        }
        return $this->dao;
    }

    /**
     * @return WorkspaceNotificationRegistration[]
     */
    public function listRegistrations(): array
    {
        return $this->getDao()->listRegistrations();
    }

    /**
     * @return WorkspaceNotificationRegistration[]
     */
    public function listActiveRegistrations(): array
    {
        return $this->getDao()->listActiveRegistrations();
    }

    public function getRegistration(int $id): ?WorkspaceNotificationRegistration
    {
        return $this->getDao()->getRegistration($id);
    }

    public function decryptWebhookUrl(WorkspaceNotificationRegistration $registration): ?string
    {
        return self::encryptionEnabled()
            ? self::getCryptographer()->decrypt($registration->getWebhookUrl())
            : $registration->getWebhookUrl();
    }

    /**
     * @param array<string,mixed> $row
     */
    public function createRegistration(array $row): WorkspaceNotificationRegistration
    {
        $registration = new WorkspaceNotificationRegistration();
        $registration->setCreatedAt(new DateTime());
        $this->applyPayload($registration, $row, true);
        return $this->getDao()->saveRegistration($registration);
    }

    /**
     * @param array<string,mixed> $row
     */
    public function updateRegistration(WorkspaceNotificationRegistration $registration, array $row): WorkspaceNotificationRegistration
    {
        $this->applyPayload($registration, $row, false);
        return $this->getDao()->saveRegistration($registration);
    }

    /**
     * @param array<string,mixed> $row
     */
    private function applyPayload(WorkspaceNotificationRegistration $registration, array $row, bool $isNew): void
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
    private function syncSubunits(WorkspaceNotificationRegistration $registration, array $subunitIds): void
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

    public static function maskWebhookUrl(?string $url, ?string $providerId = null): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }
        $registry = new \OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookProviderRegistry();
        if ($providerId !== null && $registry->has($providerId)) {
            return $registry->get($providerId)->maskUrl($url);
        }
        foreach ($registry->all() as $provider) {
            if ($provider->validateUrl($url)) {
                return $provider->maskUrl($url);
            }
        }
        return \OrangeHRM\WorkspaceNotifications\Service\Webhook\SlackWebhookProvider::genericMask($url);
    }
}
