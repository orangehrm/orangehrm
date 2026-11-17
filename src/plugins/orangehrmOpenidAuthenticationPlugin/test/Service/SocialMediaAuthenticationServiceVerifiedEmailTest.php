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

namespace OrangeHRM\Tests\OpenidAuthentication\Service;

use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\OpenIdProvider;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Logger\Logger;
use OrangeHRM\Framework\Services;
use OrangeHRM\OpenidAuthentication\Exception\OpenIdConnectAuthenticationException;
use OrangeHRM\OpenidAuthentication\Service\SocialMediaAuthenticationService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Yaml\Yaml;

/**
 * @group OpenId
 * @group Service
 */
class SocialMediaAuthenticationServiceVerifiedEmailTest extends KernelTestCase
{
    private const SUB = '6c1b2f0e-0d7e-4a55-9a8e-2f3b8d4c1a77';
    private const EMAIL = 'manul@orangehrm.us.com';

    private SocialMediaAuthenticationService $service;
    private array $warnings = [];

    protected function setUp(): void
    {
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR)
            . '/orangehrmOpenidAuthenticationPlugin/test/fixtures/AuthProviderExtraDetails.yml'
        );
        $this->service = new SocialMediaAuthenticationService();
        $this->warnings = [];
    }

    private function mockServices(): void
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['warning'])
            ->getMock();
        $logger->method('warning')->willReturnCallback(function ($message) {
            $this->warnings[] = $message;
        });

        $this->createKernelWithMockServices([Services::LOGGER => $logger]);
    }

    private function provider(bool $requireVerifiedEmail = true, bool $requireMatchingSubject = true): OpenIdProvider
    {
        $provider = new OpenIdProvider();
        $provider->setId(1);
        $provider->setProviderName('Test IdP');
        $provider->setProviderUrl('https://idp.example');
        $provider->setStatus(true);
        $provider->setRequireVerifiedEmail($requireVerifiedEmail);
        $provider->setRequireMatchingSubject($requireMatchingSubject);
        return $provider;
    }

    /**
     * @return array<string, array{0: array, 1: array}>
     */
    public function acceptedClaimsProvider(): array
    {
        $sub = ['sub' => self::SUB];
        return [
            'userinfo email_verified true' => [
                $sub,
                ['sub' => self::SUB, 'email' => self::EMAIL, 'email_verified' => true],
            ],
            'userinfo email_verified string "true" (e.g. Cognito)' => [
                $sub,
                ['sub' => self::SUB, 'email' => self::EMAIL, 'email_verified' => 'true'],
            ],
            'verified only in the ID token' => [
                ['sub' => self::SUB, 'email' => self::EMAIL, 'email_verified' => true],
                ['sub' => self::SUB, 'email' => self::EMAIL],
            ],
            'verified in both' => [
                ['sub' => self::SUB, 'email' => self::EMAIL, 'email_verified' => true],
                ['sub' => self::SUB, 'email' => self::EMAIL, 'email_verified' => true],
            ],
            'ID token email differs only in case' => [
                ['sub' => self::SUB, 'email' => 'Manul@OrangeHRM.us.com'],
                ['sub' => self::SUB, 'email' => self::EMAIL, 'email_verified' => true],
            ],
        ];
    }

    /**
     * @dataProvider acceptedClaimsProvider
     */
    public function testGetVerifiedEmailAccepts(array $idTokenClaims, array $userInfo): void
    {
        $this->mockServices();
        $this->assertSame(
            self::EMAIL,
            $this->service->getVerifiedEmail((object)$idTokenClaims, (object)$userInfo, $this->provider())
        );
        $this->assertSame([], $this->warnings);
    }

    /**
     * @return array<string, array{0: mixed, 1: mixed, 2: string}>
     */
    public function rejectedClaimsProvider(): array
    {
        $sub = (object)['sub' => self::SUB];
        $info = fn (array $extra) => (object)(['sub' => self::SUB, 'email' => self::EMAIL] + $extra);
        return [
            'email_verified false' => [$sub, $info(['email_verified' => false]), 'not true'],
            'email_verified string "false"' => [$sub, $info(['email_verified' => 'false']), 'not true'],
            'email_verified null' => [$sub, $info(['email_verified' => null]), 'not true'],
            'email_verified 1' => [$sub, $info(['email_verified' => 1]), 'not true'],
            'email_verified "1"' => [$sub, $info(['email_verified' => '1']), 'not true'],
            'email_verified "TRUE"' => [$sub, $info(['email_verified' => 'TRUE']), 'not true'],
            'no email_verified claim anywhere' => [$sub, $info([]), 'no email_verified claim'],
            'ID token false, userinfo true' => [
                (object)['sub' => self::SUB, 'email_verified' => false],
                $info(['email_verified' => true]),
                'not true',
            ],
            'userinfo false, ID token true' => [
                (object)['sub' => self::SUB, 'email_verified' => true],
                $info(['email_verified' => false]),
                'not true',
            ],
            'userinfo sub differs from ID token sub' => [
                $sub,
                (object)['sub' => 'someone-else', 'email' => self::EMAIL, 'email_verified' => true],
                'subject',
            ],
            'userinfo without sub' => [
                $sub,
                (object)['email' => self::EMAIL, 'email_verified' => true],
                'subject',
            ],
            'ID token without sub' => [
                (object)[],
                $info(['email_verified' => true]),
                'subject',
            ],
            'empty sub in both' => [
                (object)['sub' => ''],
                (object)['sub' => '', 'email' => self::EMAIL, 'email_verified' => true],
                'subject',
            ],
            'non-string sub' => [
                (object)['sub' => 12345],
                (object)['sub' => 12345, 'email' => self::EMAIL, 'email_verified' => true],
                'subject',
            ],
            'no email' => [$sub, (object)['sub' => self::SUB, 'email_verified' => true], 'no email'],
            'empty email' => [
                $sub,
                (object)['sub' => self::SUB, 'email' => '', 'email_verified' => true],
                'no email',
            ],
            'blank email' => [
                $sub,
                (object)['sub' => self::SUB, 'email' => '   ', 'email_verified' => true],
                'no email',
            ],
            'email is not a string' => [
                $sub,
                (object)['sub' => self::SUB, 'email' => [self::EMAIL], 'email_verified' => true],
                'no email',
            ],
            'ID token email differs from userinfo email' => [
                (object)['sub' => self::SUB, 'email' => 'other@orangehrm.us.com', 'email_verified' => true],
                $info(['email_verified' => true]),
                'differs',
            ],
            'ID token verified without email, userinfo email without claim' => [
                (object)['sub' => self::SUB, 'email_verified' => true],
                $info([]),
                'no email_verified claim',
            ],
            'no ID token claims' => [[], $info(['email_verified' => true]), 'missing'],
            'no userinfo response' => [$sub, null, 'missing'],
        ];
    }

    /**
     * @dataProvider rejectedClaimsProvider
     * @param mixed $idTokenClaims
     * @param mixed $userInfo
     */
    public function testGetVerifiedEmailRejects($idTokenClaims, $userInfo, string $reason): void
    {
        $this->mockServices();
        try {
            $this->service->getVerifiedEmail($idTokenClaims, $userInfo, $this->provider());
            $this->fail('Expected the claims to be refused');
        } catch (OpenIdConnectAuthenticationException $e) {
            $this->assertSame($this->expectedMessage($reason), $e->getMessage());
        }
        $this->assertCount(1, $this->warnings);
        $this->assertStringContainsString('provider id 1', $this->warnings[0]);
        $this->assertStringContainsString($reason, $this->warnings[0]);
        $this->assertStringNotContainsString(self::EMAIL, $this->warnings[0], 'the email is not logged');
    }

    /**
     * The message the user sees for the reason logged for the administrator.
     */
    private function expectedMessage(string $reason): string
    {
        switch ($reason) {
            case 'not true':
            case 'no email_verified claim':
                return OpenIdConnectAuthenticationException::emailNotVerified('Test IdP')->getMessage();
            case 'subject':
            case 'differs':
                return OpenIdConnectAuthenticationException::inconsistentUserDetails('Test IdP')->getMessage();
            case 'no email':
                return OpenIdConnectAuthenticationException::emailNotShared('Test IdP')->getMessage();
            case 'missing':
                return OpenIdConnectAuthenticationException::signInFailed('Test IdP')->getMessage();
        }
        $this->fail("Unknown reason $reason");
    }

    /**
     * With both checks off, sign-in works as before 5.9.1: the userinfo email is used as it is.
     */
    public function testBothChecksOffKeepsPreviousBehaviour(): void
    {
        $this->mockServices();
        $cases = [
            'no email_verified claim' => [['sub' => self::SUB], ['sub' => self::SUB, 'email' => self::EMAIL]],
            'email_verified false' => [
                ['sub' => self::SUB],
                ['sub' => self::SUB, 'email' => self::EMAIL, 'email_verified' => false],
            ],
            'sub differs' => [['sub' => self::SUB], ['sub' => 'someone-else', 'email' => self::EMAIL]],
            'no sub anywhere' => [[], ['email' => self::EMAIL]],
            'ID token email differs' => [
                ['sub' => self::SUB, 'email' => 'other@orangehrm.us.com'],
                ['sub' => self::SUB, 'email' => self::EMAIL],
            ],
        ];
        foreach ($cases as $name => [$idTokenClaims, $userInfo]) {
            $this->assertSame(
                self::EMAIL,
                $this->service->getVerifiedEmail((object)$idTokenClaims, (object)$userInfo, $this->provider(false, false)),
                $name
            );
        }
        $this->assertSame([], $this->warnings);
    }

    public function testBothChecksOffStillNeedsAnEmail(): void
    {
        $this->mockServices();
        $this->expectExceptionMessage(OpenIdConnectAuthenticationException::emailNotShared('Test IdP')->getMessage());
        $this->service->getVerifiedEmail(
            (object)['sub' => self::SUB],
            (object)['sub' => self::SUB],
            $this->provider(false, false)
        );
    }

    public function testVerifiedEmailOnlyIgnoresSubject(): void
    {
        $this->mockServices();
        $this->assertSame(
            self::EMAIL,
            $this->service->getVerifiedEmail(
                (object)['sub' => self::SUB],
                (object)['sub' => 'someone-else', 'email' => self::EMAIL, 'email_verified' => true],
                $this->provider(true, false)
            )
        );

        $this->expectExceptionMessage(OpenIdConnectAuthenticationException::emailNotVerified('Test IdP')->getMessage());
        $this->service->getVerifiedEmail(
            (object)['sub' => self::SUB],
            (object)['sub' => 'someone-else', 'email' => self::EMAIL, 'email_verified' => false],
            $this->provider(true, false)
        );
    }

    public function testMatchingSubjectOnlyIgnoresEmailVerification(): void
    {
        $this->mockServices();
        $this->assertSame(
            self::EMAIL,
            $this->service->getVerifiedEmail(
                (object)['sub' => self::SUB],
                (object)['sub' => self::SUB, 'email' => self::EMAIL, 'email_verified' => false],
                $this->provider(false, true)
            )
        );

        $this->expectExceptionMessage(OpenIdConnectAuthenticationException::inconsistentUserDetails('Test IdP')->getMessage());
        $this->service->getVerifiedEmail(
            (object)['sub' => self::SUB],
            (object)['sub' => 'someone-else', 'email' => self::EMAIL, 'email_verified' => true],
            $this->provider(false, true)
        );
    }

    /**
     * The login page translates the message by its English text, so each one needs a lang string.
     */
    public function testEveryMessageHasALangString(): void
    {
        $langStrings = Yaml::parseFile(
            realpath(__DIR__ . '/../../../../../installer/Migration/V5_9_1/lang-string/auth.yaml')
        )['langStrings'];
        $values = array_column($langStrings, 'value', 'unitId');
        foreach ([
            OpenIdConnectAuthenticationException::providerNotAvailable(),
            OpenIdConnectAuthenticationException::signInFailed('Test IdP'),
            OpenIdConnectAuthenticationException::inconsistentUserDetails('Test IdP'),
            OpenIdConnectAuthenticationException::emailNotShared('Test IdP'),
            OpenIdConnectAuthenticationException::emailNotVerified('Test IdP'),
            OpenIdConnectAuthenticationException::accountNotAvailable(),
        ] as $exception) {
            $this->assertContains($exception->getMessage(), $values);
        }
    }

    public function testGetUserForAuthenticateMatchesUsernameIgnoringCase(): void
    {
        $credential = new UserCredential('MANUL@OrangeHRM.us.com');
        $user = $this->service->getUserForAuthenticate($credential);
        $this->assertSame(2, $user->getId());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public function lookAlikeUsernameProvider(): array
    {
        return [
            // The database collation (utf8 *_general_ci) can treat these as equal to the stored username
            'accented letter' => ['mánul@orangehrm.us.com'],
            'trailing space' => ['manul@orangehrm.us.com '],
            // mb_strtolower() folds the Kelvin sign (U+212A) to "k"
            'Kelvin sign' => ["\u{212A}ayla@orangehrm.us.com"],
        ];
    }

    /**
     * The user search is mocked to return the stored user, as a case- and accent-insensitive
     * collation would, so the check after the lookup is what is tested.
     * @dataProvider lookAlikeUsernameProvider
     */
    public function testGetUserForAuthenticateRefusesLookAlikeUsername(string $username): void
    {
        $stored = strpos($username, "\u{212A}") === 0 ? 'kayla@orangehrm.us.com' : self::EMAIL;
        $user = $this->getMockBuilder(User::class)
            ->onlyMethods(['getUserName', 'isDeleted'])
            ->getMock();
        $user->method('getUserName')->willReturn($stored);
        $user->method('isDeleted')->willReturn(false);
        $service = $this->serviceWithUsers([$user]);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('No User Found');
        $service->getUserForAuthenticate(new UserCredential($username));
    }

    /**
     * @param User[] $users
     */
    private function serviceWithUsers(array $users): SocialMediaAuthenticationService
    {
        $userDao = $this->getMockBuilder(UserDao::class)
            ->onlyMethods(['searchSystemUsers'])
            ->getMock();
        $userDao->method('searchSystemUsers')->willReturn($users);
        $service = $this->getMockBuilder(SocialMediaAuthenticationService::class)
            ->onlyMethods(['getUserDao'])
            ->getMock();
        $service->method('getUserDao')->willReturn($userDao);
        return $service;
    }

    /**
     * @return array<string, array{0: string|null}>
     */
    public function emptyUsernameProvider(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
        ];
    }

    /**
     * An empty username must not reach the user search, which skips its username filter when empty
     * and would return every user (a single-user install would sign that user in).
     * @dataProvider emptyUsernameProvider
     */
    public function testGetUserForAuthenticateRefusesEmptyUsername(?string $username): void
    {
        $service = $this->serviceWithUsers([$this->getEntityReference(User::class, 2)]);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('No User Found');
        $service->getUserForAuthenticate(new UserCredential($username));
    }
}
