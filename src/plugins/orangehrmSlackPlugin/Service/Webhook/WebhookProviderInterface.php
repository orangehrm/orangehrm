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

use OrangeHRM\Slack\Service\Formatter\MessageFormatterInterface;

/**
 * Contract every webhook-based notification provider must implement.
 *
 * Slack is the only implementation shipped with 5.9. The seam is here so that
 * subsequent releases can add Google Chat / Microsoft Teams / Discord without
 * touching the orchestrator, schema, or API contract — each new provider is
 * a single new class registered with the {@see WebhookProviderRegistry}.
 *
 * Implementations should be stateless beyond a lazily-created HTTP client.
 */
interface WebhookProviderInterface
{
    /**
     * Stable identifier persisted in `ohrm_slack_registration.provider`
     * (e.g. 'slack', 'google_chat'). The registry uses this to resolve
     * the right implementation for a given registration row.
     */
    public function getProviderId(): string;

    /**
     * Shape-check the incoming-webhook URL the admin entered.
     * Each provider knows its own host + path layout.
     */
    public function validateUrl(string $url): bool;

    /**
     * POST the message text to the provider. Implementations translate the
     * provider-specific success signal (e.g. Slack's literal `ok`, Google
     * Chat's JSON `Message` object) into a uniform {@see WebhookDeliveryResult}.
     */
    public function send(string $webhookUrl, string $text): WebhookDeliveryResult;

    /**
     * Return a display-safe rendering of $url with the secret stripped.
     *
     * Each provider knows where in its URL shape the secret lives:
     *   - Slack: the third path segment after `/services/`
     *   - Google Chat: the `key=` + `token=` query params
     *   - Future providers: their own conventions
     *
     * Output is used in two places that MUST agree:
     *   1. {@see SlackRegistrationModel::toArray()} serializes this back to
     *      the FE on every list-read (the admin never sees the secret again
     *      once it leaves the form).
     *   2. The FE's duplicate-detection compares the just-typed URL (masked
     *      client-side) against the masked URL returned for existing rows.
     *      Drift between this mask and the FE mirror produces false negatives.
     */
    public function maskUrl(string $url): string;

    /**
     * Return the formatter that produces messages in this provider's native
     * markup. The orchestrator asks every provider for its formatter rather
     * than holding one statically — that way new platforms drop in cleanly
     * (one provider class + one formatter class, no orchestrator changes).
     *
     * Implementations may return a shared formatter instance: Slack + Google
     * Chat both return {@see \OrangeHRM\Slack\Service\Formatter\SlackMessageFormatter}
     * because they accept identical markdown.
     */
    public function getFormatter(): MessageFormatterInterface;
}
