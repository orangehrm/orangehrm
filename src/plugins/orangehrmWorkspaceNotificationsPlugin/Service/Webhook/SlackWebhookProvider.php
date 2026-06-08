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

namespace OrangeHRM\WorkspaceNotifications\Service\Webhook;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\MessageFormatterInterface;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\SlackMessageFormatter;

class SlackWebhookProvider implements WebhookProviderInterface
{
    public const SLACK_WEBHOOK_URL_REGEX =
        '#^https://hooks\.slack\.com/services/[A-Z0-9]+/[A-Z0-9]+/[A-Za-z0-9]+$#';

    private const CONNECT_TIMEOUT = 3;
    private const TOTAL_TIMEOUT = 5;

    private ?Client $client = null;
    private ?SlackMessageFormatter $formatter = null;

    public function getProviderId(): string
    {
        return WorkspaceNotificationRegistration::PROVIDER_SLACK;
    }

    public function getFormatter(): MessageFormatterInterface
    {
        if ($this->formatter === null) {
            $this->formatter = new SlackMessageFormatter();
        }
        return $this->formatter;
    }

    public function validateUrl(string $url): bool
    {
        return (bool)preg_match(self::SLACK_WEBHOOK_URL_REGEX, $url);
    }

    public function maskUrl(string $url): string
    {
        if (preg_match('#^(https://hooks\.slack\.com/services/[A-Z0-9]+/[A-Z0-9]+)/.+$#', $url, $m)) {
            return $m[1] . '/…';
        }
        return self::genericMask($url);
    }

    public static function genericMask(string $url): string
    {
        $parts = explode('/', $url);
        if (count($parts) > 2) {
            array_pop($parts);
            return implode('/', $parts) . '/…';
        }
        return '…';
    }

    public function send(string $webhookUrl, string $text): WebhookDeliveryResult
    {
        try {
            $response = $this->getClient()->post($webhookUrl, [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => ['text' => $text],
                'http_errors' => false,
            ]);
        } catch (GuzzleException $e) {
            return WebhookDeliveryResult::failure(
                'Failed to reach Slack: ' . $this->scrubUrl($e->getMessage(), $webhookUrl)
            );
        }

        $body = trim((string)$response->getBody());
        if ($response->getStatusCode() === 200 && $body === 'ok') {
            return WebhookDeliveryResult::success();
        }

        $reason = $body !== '' ? $this->scrubUrl($body, $webhookUrl) : ('HTTP ' . $response->getStatusCode());
        return WebhookDeliveryResult::failure('Slack rejected the message: ' . $reason);
    }

    private function scrubUrl(string $message, string $webhookUrl): string
    {
        if ($webhookUrl === '' || strpos($message, $webhookUrl) === false) {
            return $message;
        }
        return str_replace($webhookUrl, $this->maskUrl($webhookUrl), $message);
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

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
