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

namespace OrangeHRM\OpenidAuthentication\Service;

use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Admin\Dto\UserSearchFilterParams;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Authentication\Traits\Service\AuthenticationServiceTrait;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Utility\EncryptionHelperTrait;
use OrangeHRM\Entity\AuthProviderExtraDetails;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\OpenIdProvider;
use OrangeHRM\Entity\OpenIdUserIdentity;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\OpenidAuthentication\Dao\AuthProviderDao;
use OrangeHRM\OpenidAuthentication\Dto\ProviderSearchFilterParams;
use OrangeHRM\OpenidAuthentication\Exception\OpenIdConnectAuthenticationException;
use OrangeHRM\OpenidAuthentication\OpenID\OpenIDConnectClient;
use OrangeHRM\OpenidAuthentication\Traits\Service\SocialMediaAuthenticationServiceTrait;

class SocialMediaAuthenticationService
{
    use SocialMediaAuthenticationServiceTrait;
    use AuthenticationServiceTrait;
    use EncryptionHelperTrait;
    use AuthUserTrait;
    use LoggerTrait;

    private AuthenticationService $authenticationService;
    private AuthProviderDao $authProviderDao;
    private UserDao $userDao;

    public const SCOPE = 'email';
    public const CLAIM_SUBJECT = 'sub';
    public const CLAIM_EMAIL = 'email';
    public const CLAIM_EMAIL_VERIFIED = 'email_verified';

    /**
     * @return AuthProviderDao
     */
    public function getAuthProviderDao(): AuthProviderDao
    {
        return $this->authProviderDao ??= new AuthProviderDao();
    }

    /**
     * @return UserDao
     */
    public function getUserDao(): UserDao
    {
        return $this->userDao ??= new UserDao();
    }

    /**
     * @param AuthProviderExtraDetails $provider
     * @param string $scope
     * @param string $redirectUrl
     *
     * @return OpenIDConnectClient
     */
    public function initiateAuthentication(AuthProviderExtraDetails $provider, string $scope, string $redirectUrl): OpenIDConnectClient
    {
        $oidcClient = new OpenIDConnectClient(
            $provider->getOpenIdProvider()->getProviderUrl(),
            $provider->getClientId(),
            self::encryptionEnabled()
                ? self::getCryptographer()->decrypt($provider->getClientSecret())
                : $provider->getClientSecret(),
        );

        $oidcClient->addScope([$scope]);
        $oidcClient->setRedirectURL($redirectUrl);

        return $oidcClient;
    }

    /**
     * @return string
     */
    public function getRedirectURL(): string
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);
        return $urlGenerator->generate('auth_oidc_login_redirect', [], UrlGenerator::ABSOLUTE_URL);
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return self::SCOPE;
    }

    /**
     * @param UserCredential $userCredential
     * @return User[]
     */
    private function getSystemUsers(UserCredential $userCredential): array
    {
        $userSearchFilterParams = new UserSearchFilterParams();
        $userSearchFilterParams->setUsername($userCredential->getUsername());

        return $this->getUserDao()->searchSystemUsers($userSearchFilterParams);
    }

    /**
     * Returns the email address to match against a username, applying the provider's sign-in checks.
     * With both checks off this is the userinfo `email`, as before 5.9.1.
     *
     * - Require matching subject: the userinfo `sub` must equal the ID token `sub`
     *   (OpenID Connect Core 1.0, 5.3.2), so both responses describe the same user.
     * - Require verified email: `email_verified` must be true for the email used. An ID token email, if
     *   present, must equal the userinfo email; an explicit non-true `email_verified` in either response
     *   refuses; a true value counts only from a response that also carries the email.
     *
     * Runs before any local user is looked up, so its refusals cannot reveal whether an account exists.
     *
     * @param mixed $idTokenClaims verified ID token claims
     * @param mixed $userInfo userinfo response
     * @param OpenIdProvider $provider
     *
     * @return string
     * @throws OpenIdConnectAuthenticationException
     */
    public function getVerifiedEmail($idTokenClaims, $userInfo, OpenIdProvider $provider): string
    {
        if (!is_object($idTokenClaims) || !is_object($userInfo)) {
            $this->logRefusal($provider, 'missing ID token claims or userinfo response');
            throw OpenIdConnectAuthenticationException::signInFailed($provider->getProviderName());
        }

        if ($provider->isRequireMatchingSubject()) {
            $subject = $idTokenClaims->{self::CLAIM_SUBJECT} ?? null;
            if (!is_string($subject) || $subject === ''
                || ($userInfo->{self::CLAIM_SUBJECT} ?? null) !== $subject) {
                $this->logRefusal($provider, 'userinfo subject does not match the ID token subject');
                throw OpenIdConnectAuthenticationException::inconsistentUserDetails($provider->getProviderName());
            }
        }

        $email = $userInfo->{self::CLAIM_EMAIL} ?? null;
        if (!is_string($email) || trim($email) === '') {
            $this->logRefusal($provider, 'no email claim in the userinfo response');
            throw OpenIdConnectAuthenticationException::emailNotShared($provider->getProviderName());
        }

        if ($provider->isRequireVerifiedEmail()) {
            $this->checkEmailVerified($idTokenClaims, $userInfo, $email, $provider);
        }

        return $email;
    }

    /**
     * @param object $idTokenClaims
     * @param object $userInfo
     * @param string $email userinfo email
     * @param OpenIdProvider $provider
     * @throws OpenIdConnectAuthenticationException
     */
    private function checkEmailVerified(object $idTokenClaims, object $userInfo, string $email, OpenIdProvider $provider): void
    {
        if (property_exists($idTokenClaims, self::CLAIM_EMAIL)
            && (!is_string($idTokenClaims->{self::CLAIM_EMAIL})
                || !$this->isSameIdentifier($idTokenClaims->{self::CLAIM_EMAIL}, $email))) {
            $this->logRefusal($provider, 'ID token email differs from the userinfo email');
            throw OpenIdConnectAuthenticationException::inconsistentUserDetails($provider->getProviderName());
        }

        $verifiedForEmail = false;
        foreach ([$userInfo, $idTokenClaims] as $claims) {
            if (!property_exists($claims, self::CLAIM_EMAIL_VERIFIED)) {
                continue;
            }
            $verified = $claims->{self::CLAIM_EMAIL_VERIFIED};
            // Some providers (e.g. Amazon Cognito userinfo) send the string "true"
            if ($verified !== true && $verified !== 'true') {
                $this->logRefusal($provider, 'email_verified is not true');
                throw OpenIdConnectAuthenticationException::emailNotVerified($provider->getProviderName());
            }
            if (property_exists($claims, self::CLAIM_EMAIL)) {
                $verifiedForEmail = true;
            }
        }

        if (!$verifiedForEmail) {
            $this->logRefusal(
                $provider,
                'no email_verified claim for the email; turn off "Require verified email" for this provider'
                . ' only if its users cannot choose their own email address'
            );
            throw OpenIdConnectAuthenticationException::emailNotVerified($provider->getProviderName());
        }
    }

    /**
     * Logs why a sign-in was refused, for the administrator. The user only sees the exception message.
     * The email address is not logged.
     *
     * @param OpenIdProvider $provider
     * @param string $reason
     */
    public function logRefusal(OpenIdProvider $provider, string $reason): void
    {
        $this->getLogger()->warning(
            sprintf('OpenID Connect sign-in refused (provider id %d): %s', $provider->getId(), $reason)
        );
    }

    /**
     * @param string $a
     * @param string $b
     * @return bool
     */
    private function isSameIdentifier(string $a, string $b): bool
    {
        // ASCII-only case folding: mb_strtolower() would also fold e.g. the Kelvin sign (U+212A) to "k"
        return strcasecmp($a, $b) === 0;
    }

    /**
     * @param UserCredential $userCredentials
     *
     * @return User
     * @throws AuthenticationException
     */
    public function getUserForAuthenticate(UserCredential $userCredentials): User
    {
        $username = $userCredentials->getUsername();
        if ($username === null || $username === '') {
            throw AuthenticationException::noUserFound();
        }

        $users = $this->getSystemUsers($userCredentials);
        if (empty($users)) {
            throw AuthenticationException::noUserFound();
        }

        if (sizeof($users) > 1) {
            throw AuthenticationException::multipleUserReturned();
        }

        $user = $users[0];

        if (!$user instanceof User || $user->isDeleted()) {
            throw AuthenticationException::invalidCredentials();
        } elseif (!$this->isSameIdentifier($user->getUserName(), $username)) {
            // The database collation also ignores accents and trailing spaces
            throw AuthenticationException::noUserFound();
        } else {
            if (!$user->getStatus()) {
                throw AuthenticationException::userDisabled();
            } elseif ($user->getEmpNumber() === null) {
                throw AuthenticationException::employeeNotAssigned();
            } elseif ($user->getEmployee()->getEmployeeTerminationRecord() instanceof EmployeeTerminationRecord) {
                throw AuthenticationException::employeeTerminated();
            }
            return $user;
        }
    }

    /**
     * @param User $user
     * @param OpenIdProvider $provider
     *
     * @return OpenIdUserIdentity
     * @deprecated Not used for sign-in. Fresh installs key ohrm_openid_user_identity on user_id only, so a
     *             row per sign-in hits a duplicate key; a provider/subject link needs its own schema.
     */
    public function setOIDCUserIdentity(User $user, OpenIdProvider $provider): OpenIdUserIdentity
    {
        $openIdUserIdentity = new OpenIdUserIdentity();
        $openIdUserIdentity->setUser($user);
        $openIdUserIdentity->setOpenIdProvider($provider);

        return $this->getAuthProviderDao()->saveUserIdentity($openIdUserIdentity);
    }

    /**
     * @param User $user
     *
     * @return bool
     * @throws AuthenticationException
     */
    public function handleOIDCAuthentication(User $user): bool
    {
        return $this->getAuthenticationService()->setCredentialsForUser($user);
    }

    /**
     * @return bool
     */
    public function isSocialMediaAuthEnable(): bool
    {
        $providerSearchFilterParams = new ProviderSearchFilterParams();
        $providerSearchFilterParams->setName(null);
        $providerSearchFilterParams->setStatus(true);

        $count = $this->getAuthProviderDao()->getAuthProviderCount($providerSearchFilterParams);
        return $count > 0;
    }
}
