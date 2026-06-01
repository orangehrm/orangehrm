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

namespace OrangeHRM\Slack\Service\Webhook;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OrangeHRM\Slack\Service\Webhook\SlackDeliveryResult;

class SlackWebhookClient
{
    public const SLACK_WEBHOOK_URL_REGEX =
        '#^https://hooks\.slack\.com/services/[A-Z0-9]+/[A-Z0-9]+/[A-Za-z0-9]+$#';

    public const WEBHOOK_URL_MAX_LENGTH = 512;

    private const CONNECT_TIMEOUT = 3;
    private const TOTAL_TIMEOUT = 5;

    private ?Client $client = null;

    public static function isValidWebhookUrl(string $url): bool
    {
        return (bool)preg_match(self::SLACK_WEBHOOK_URL_REGEX, $url);
    }

    public function send(string $webhookUrl, string $text): SlackDeliveryResult
    {
        try {
            $response = $this->getClient()->post($webhookUrl, [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => ['text' => $text],
                'http_errors' => false,
            ]);
        } catch (GuzzleException $e) {
            return SlackDeliveryResult::failure('Failed to reach Slack: ' . $e->getMessage());
        }

        $body = trim((string)$response->getBody());
        if ($response->getStatusCode() === 200 && $body === 'ok') {
            return SlackDeliveryResult::success();
        }

        $reason = $body !== '' ? $body : ('HTTP ' . $response->getStatusCode());
        return SlackDeliveryResult::failure('Slack rejected the message: ' . $reason);
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client([
                'timeout' => self::TOTAL_TIMEOUT,
                'connect_timeout' => self::CONNECT_TIMEOUT,
            ]);
        }
        return $this->client;
    }

    /**
     * Test seam — allows command/tests to inject a Guzzle client.
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
