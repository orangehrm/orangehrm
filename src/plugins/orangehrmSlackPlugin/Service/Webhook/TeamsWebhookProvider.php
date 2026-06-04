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
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Service\Formatter\MessageFormatterInterface;
use OrangeHRM\Slack\Service\Formatter\TeamsMessageFormatter;

/**
 * Microsoft Teams provider. URL shape is a Power Automate workflow trigger:
 *
 *   https://prod-XX.{region}.logic.azure.com:443/workflows/{wf_id}/triggers/manual/paths/invoke?
 *      api-version=2016-06-01&sp=...&sig=...
 *
 * The legacy "Office 365 Connector" path
 * (`https://{tenant}.webhook.office.com/webhookb2/…`) is being retired by
 * Microsoft and is intentionally NOT accepted here — admins are nudged onto
 * the supported workflow path.
 *
 * Success signal: HTTP 2xx with any body (Power Automate workflows commonly
 * return 202 Accepted with an empty body; some return 200 with a JSON object).
 * We don't probe the body — any 2xx is treated as accepted.
 */
class TeamsWebhookProvider implements WebhookProviderInterface
{
    /**
     * Power Automate workflow trigger URL — the modern, Microsoft-supported
     * path for posting to a Teams channel.
     */
    /**
     * Power Automate URLs have a multi-label host — typically
     * `prod-<N>.<region>.logic.azure.com` (e.g. `prod-12.westus.logic.azure.com`),
     * so the regex allows one OR more dot-separated labels before `logic.azure.com`.
     */
    private const URL_REGEX =
        '#^https://(?:[a-z0-9-]+\.)+logic\.azure\.com(:[0-9]+)?/workflows/[a-z0-9-]+/triggers/[a-zA-Z0-9_]+/paths/invoke\?[^\s]+$#';

    private const CONNECT_TIMEOUT = 3;
    private const TOTAL_TIMEOUT = 5;

    private ?Client $client = null;
    private ?TeamsMessageFormatter $formatter = null;

    public function getProviderId(): string
    {
        return SlackRegistration::PROVIDER_TEAMS;
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
                // The simplest Power Automate workflow shape that's compatible
                // with the "Post a message in a chat or channel" action: a JSON
                // body with a single `text` field. More elaborate workflows
                // (Adaptive Cards) are a future enhancement.
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

        // Teams / Power Automate error bodies are typically JSON `{error: {message: "..."}}`,
        // but for 401/403 they're sometimes plaintext. Prefer the structured
        // message when we can decode it; fall back to the raw body or HTTP code.
        $body = (string)$response->getBody();
        $decoded = json_decode($body, true);
        $reason = is_array($decoded) && isset($decoded['error']['message'])
            ? $decoded['error']['message']
            : ($body !== '' ? $body : ('HTTP ' . $status));
        return WebhookDeliveryResult::failure(
            'Teams rejected the message: ' . $this->scrubUrl($reason, $webhookUrl)
        );
    }

    /**
     * Replace any occurrence of the raw webhook URL in $message with its
     * masked form. Teams workflow URLs carry the `sig=` HMAC in the query
     * string — letting that leak into slack_log.error_message would be the
     * same severity as leaking a password.
     */
    private function scrubUrl(string $message, string $webhookUrl): string
    {
        if ($webhookUrl === '' || strpos($message, $webhookUrl) === false) {
            return $message;
        }
        return str_replace($webhookUrl, $this->maskUrl($webhookUrl), $message);
    }

    /**
     * Keep the workflow path (low-sensitivity, lets the admin recognise the
     * row) and strip the entire query string — that's where `sig=` (the
     * authentication HMAC) lives.
     *
     *   https://prod-12.westus.logic.azure.com/workflows/abc/triggers/manual/paths/invoke?...&sig=...
     *                                                         ↓
     *   https://prod-12.westus.logic.azure.com/workflows/abc/triggers/manual/paths/invoke?…
     */
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
