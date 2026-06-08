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
use OrangeHRM\WorkspaceNotifications\Service\Formatter\TeamsMessageFormatter;

class TeamsWebhookProvider implements WebhookProviderInterface
{
    private const URL_REGEX =
        '#^https://(?:[a-z0-9-]+\.)+logic\.azure\.com(:[0-9]+)?/workflows/[a-z0-9-]+/triggers/[a-zA-Z0-9_]+/paths/invoke\?[^\s]+$#';

    private const CONNECT_TIMEOUT = 3;
    private const TOTAL_TIMEOUT = 5;

    private ?Client $client = null;
    private ?TeamsMessageFormatter $formatter = null;

    public function getProviderId(): string
    {
        return WorkspaceNotificationRegistration::PROVIDER_TEAMS;
    }

    public function getFormatter(): MessageFormatterInterface
    {
        if ($this->formatter === null) {
            $this->formatter = new TeamsMessageFormatter();
        }
        return $this->formatter;
    }

    public function validateUrl(string $url): bool
    {
        if (!preg_match(self::URL_REGEX, $url)) {
            return false;
        }
        // The Power Automate signature is in the `sig` query param. Without it
        // the workflow will reject the call with 401, so refuse early.
        $query = parse_url($url, PHP_URL_QUERY);
        if (!is_string($query) || $query === '') {
            return false;
        }
        parse_str($query, $params);
        return !empty($params['sig']);
    }

    public function send(string $webhookUrl, string $text): WebhookDeliveryResult
    {
        try {
            $response = $this->getClient()->post($webhookUrl, [
                'headers' => ['Content-Type' => 'application/json; charset=UTF-8'],
                'json' => ['text' => $text],
                'http_errors' => false,
            ]);
        } catch (GuzzleException $e) {
            return WebhookDeliveryResult::failure(
                'Failed to reach Microsoft Teams: ' . $this->scrubUrl($e->getMessage(), $webhookUrl)
            );
        }

        $status = $response->getStatusCode();
        if ($status >= 200 && $status < 300) {
            return WebhookDeliveryResult::success();
        }

        $body = (string)$response->getBody();
        $decoded = json_decode($body, true);
        $reason = is_array($decoded) && isset($decoded['error']['message'])
            ? $decoded['error']['message']
            : ($body !== '' ? $body : ('HTTP ' . $status));
        return WebhookDeliveryResult::failure(
            'Teams rejected the message: ' . $this->scrubUrl($reason, $webhookUrl)
        );
    }

    private function scrubUrl(string $message, string $webhookUrl): string
    {
        if ($webhookUrl === '' || strpos($message, $webhookUrl) === false) {
            return $message;
        }
        return str_replace($webhookUrl, $this->maskUrl($webhookUrl), $message);
    }

    public function maskUrl(string $url): string
    {
        if (preg_match('#^(https://(?:[a-z0-9-]+\.)+logic\.azure\.com(?::[0-9]+)?/workflows/[a-z0-9-]+/triggers/[a-zA-Z0-9_]+/paths/invoke)\?.+$#', $url, $m)) {
            return $m[1] . '?…';
        }
        return SlackWebhookProvider::genericMask($url);
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
