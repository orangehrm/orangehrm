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

use InvalidArgumentException;
use OrangeHRM\Entity\SlackRegistration;

/**
 * Resolves a {@see WebhookProviderInterface} by the provider id stored on each
 * `SlackRegistration` row.
 *
 * Self-bootstraps with the built-in providers so adding a new provider in a
 * later release is a single `register()` call here (or, equivalently, a single
 * line in the plugin's `initialize()` if injected externally). Tests can call
 * `register()` to swap a real provider for a fake.
 */
class WebhookProviderRegistry
{
    /**
     * Fleet-wide ceiling shared by all providers. Matches the
     * `ohrm_workspace_notification_registration.webhook_url` column. If a future provider needs
     * a longer URL than this, bump the column via migration first.
     */
    public const MAX_URL_LENGTH = 512;

    /** @var array<string, WebhookProviderInterface> */
    private array $providers = [];

    public function __construct()
    {
        $this->register(new SlackWebhookProvider());
        $this->register(new GoogleChatWebhookProvider());
        $this->register(new TeamsWebhookProvider());
    }

    public function register(WebhookProviderInterface $provider): void
    {
        $this->providers[$provider->getProviderId()] = $provider;
    }

    public function has(string $providerId): bool
    {
        return isset($this->providers[$providerId]);
    }

    public function get(string $providerId): WebhookProviderInterface
    {
        if (!isset($this->providers[$providerId])) {
            throw new InvalidArgumentException(
                "No webhook provider registered for id '{$providerId}'."
            );
        }
        return $this->providers[$providerId];
    }

    /**
     * Convenience shortcut for the orchestrator and `SlackTestWebhookAPI`,
     * both of which resolve the provider from an existing registration row.
     */
    public function getForRegistration(SlackRegistration $registration): WebhookProviderInterface
    {
        return $this->get($registration->getProvider());
    }

    /**
     * @return array<string, WebhookProviderInterface>
     */
    public function all(): array
    {
        return $this->providers;
    }
}
