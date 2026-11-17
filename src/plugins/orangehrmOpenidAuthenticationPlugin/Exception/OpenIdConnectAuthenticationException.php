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

namespace OrangeHRM\OpenidAuthentication\Exception;

use OrangeHRM\Authentication\Exception\AuthenticationException;

/**
 * Reasons an OpenID Connect sign-in is refused, shown on the login page. Where the provider is known, the message
 * names it, as on the "Login with" button the user clicked.
 * Every reason up to `emailNotVerified()` is decided before a local user is looked up, so those messages
 * cannot tell whether an OrangeHRM account exists. Every refusal after the lookup uses `accountNotAvailable()`.
 */
class OpenIdConnectAuthenticationException extends AuthenticationException
{
    public const PROVIDER_NOT_AVAILABLE = 'openid_provider_not_available';
    public const SIGN_IN_FAILED = 'openid_sign_in_failed';
    public const INCONSISTENT_USER_DETAILS = 'openid_inconsistent_user_details';
    public const EMAIL_NOT_SHARED = 'openid_email_not_shared';
    public const EMAIL_NOT_VERIFIED = 'openid_email_not_verified';
    public const ACCOUNT_NOT_AVAILABLE = 'openid_account_not_available';
    public const PARAMETER_PROVIDER_NAME = 'providerName';

    /**
     * Values for the placeholders in the message, e.g. ['providerName' => 'Corp Keycloak']
     */
    private array $parameters;

    /**
     * @param string $name
     * @param string $message source lang string, may contain {providerName}
     * @param array $parameters
     */
    protected function __construct(string $name, string $message, array $parameters = [])
    {
        parent::__construct($name, $message);
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function normalize(): array
    {
        $normalized = parent::normalize();
        if (!empty($this->parameters)) {
            $message = $this->getI18NHelper()->transBySource($this->getMessage(), $this->parameters);
            // Also fills the placeholders when the message is not translated, e.g. with translation turned off
            $replacements = [];
            foreach ($this->parameters as $key => $value) {
                $replacements['{' . $key . '}'] = (string)$value;
            }
            $normalized['message'] = strtr($message, $replacements);
        }
        return $normalized;
    }

    /**
     * The provider is unknown, disabled, or the sign-in did not start from this session.
     */
    public static function providerNotAvailable(): self
    {
        return new self(
            self::PROVIDER_NOT_AVAILABLE,
            'This sign-in option is not available. Please contact your administrator.'
        );
    }

    /**
     * The exchange with the provider failed, e.g. the user cancelled, or the response could not be validated.
     */
    public static function signInFailed(string $providerName): self
    {
        return new self(
            self::SIGN_IN_FAILED,
            'Sign-in with {providerName} failed. Please try again.',
            [self::PARAMETER_PROVIDER_NAME => $providerName]
        );
    }

    /**
     * The ID token and the userinfo response do not describe the same user.
     */
    public static function inconsistentUserDetails(string $providerName): self
    {
        return new self(
            self::INCONSISTENT_USER_DETAILS,
            '{providerName} returned inconsistent user details. Please contact your administrator.',
            [self::PARAMETER_PROVIDER_NAME => $providerName]
        );
    }

    public static function emailNotShared(string $providerName): self
    {
        return new self(
            self::EMAIL_NOT_SHARED,
            '{providerName} did not share an email address for your account. Please contact your administrator.',
            [self::PARAMETER_PROVIDER_NAME => $providerName]
        );
    }

    public static function emailNotVerified(string $providerName): self
    {
        return new self(
            self::EMAIL_NOT_VERIFIED,
            'Your email address is not verified by {providerName}. Please verify it with {providerName} or contact your administrator.',
            [self::PARAMETER_PROVIDER_NAME => $providerName]
        );
    }

    /**
     * Any refusal after the local user lookup (no user, several users, deleted, disabled, no employee,
     * terminated). One message for all of them, so a sign-in attempt does not reveal whether an account exists.
     */
    public static function accountNotAvailable(): self
    {
        return new self(
            self::ACCOUNT_NOT_AVAILABLE,
            'Unable to sign in with this account. Please contact your administrator.'
        );
    }
}
