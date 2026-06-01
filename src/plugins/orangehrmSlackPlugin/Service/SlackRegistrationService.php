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
     * Apply the full payload from the UI's PUT /config in one shot:
     *  - Upsert each row in $payload (by id when present, otherwise create).
     *  - Delete any existing rows whose ID isn't in the payload.
     *
     * Payload row shape:
     *   ['id' => int|null, 'eventType' => string, 'webhookUrl' => string|null,
     *    'channelLabel' => string|null, 'subunitId' => int|null, 'active' => bool]
     *
     * @param array<int,array<string,mixed>> $payload
     */
    public function syncFromPayload(array $payload): void
    {
        $keepIds = [];
        foreach ($payload as $row) {
            $id = isset($row['id']) ? (int)$row['id'] : null;
            $registration = ($id !== null && $id > 0) ? $this->getRegistration($id) : null;
            $isNew = $registration === null;
            if ($isNew) {
                $registration = new SlackRegistration();
                $registration->setCreatedAt(new DateTime());
            }

            $registration->setEventType((string)$row['eventType']);
            $registration->setChannelLabel(isset($row['channelLabel']) ? $row['channelLabel'] : null);
            $registration->setActive(!isset($row['active']) || (bool)$row['active']);
            $registration->setUpdatedAt(new DateTime());

            $subunitId = isset($row['subunitId']) ? $row['subunitId'] : null;
            if ($subunitId !== null && $subunitId !== '' && (int)$subunitId > 0) {
                /** @var Subunit|null $subunit */
                $subunit = $this->getRepository(Subunit::class)->find((int)$subunitId);
                $registration->setSubunit($subunit);
            } else {
                $registration->setSubunit(null);
            }

            $newWebhook = isset($row['webhookUrl']) ? $row['webhookUrl'] : null;
            if ($newWebhook !== null && $newWebhook !== '') {
                $registration->setWebhookUrl($this->encryptForStorage((string)$newWebhook));
            } elseif ($isNew) {
                // Brand-new row with no webhook → API layer should have rejected it.
                // Be defensive and skip rather than persisting an empty webhook.
                continue;
            }

            $this->getDao()->saveRegistration($registration);
            $keepIds[] = $registration->getId();
        }

        $this->getDao()->deleteRegistrationsNotIn($keepIds);
    }

    public function encryptForStorage(string $plain): string
    {
        return self::encryptionEnabled()
            ? self::getCryptographer()->encrypt($plain)
            : $plain;
    }

    public static function maskWebhookUrl(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }
        if (preg_match('#^(https://hooks\.slack\.com/services/[A-Z0-9]+/[A-Z0-9]+)/.+$#', $url, $m)) {
            return $m[1] . '/…';
        }
        // Generic fallback: keep up to the second-last segment.
        $parts = explode('/', $url);
        if (count($parts) > 2) {
            array_pop($parts);
            return implode('/', $parts) . '/…';
        }
        return '…';
    }
}
