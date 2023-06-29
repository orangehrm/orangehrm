<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Tests\OAuth\Server;

use DateTime;
use League\OAuth2\Server\Exception\OAuthServerException;
use LogicException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\OAuthAccessToken;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Services;
use OrangeHRM\OAuth\Repository\AccessTokenRepository;
use OrangeHRM\OAuth\Server\BearerTokenValidator;
use OrangeHRM\OAuth\Service\PsrHttpFactoryHelper;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OAuth
 * @group Server
 */
class BearerTokenValidatorTest extends KernelTestCase
{
    private string $encryptionKey;
    private AccessTokenRepository $accessTokenRepository;

    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCoreOAuthPlugin/test/fixtures/BearerTokenValidatorTest.yaml';
        TestDataService::populate($fixture);
        $this->accessTokenRepository = new AccessTokenRepository();
        $this->encryptionKey = '9R8rdJTORQRWuSh3UbSAImWrFTU0Yd7RS+A4s+urPEM=';
    }

    public function testValidateAuthorizationWithoutAuthorizeHeader(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->never())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2023-04-28 15:59:59'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create('/api/v2/pim/employees', 'GET', [], [], [], []);
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator($this->accessTokenRepository, $this->encryptionKey);
        try {
            $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
            $this->fail();
        } catch (OAuthServerException $e) {
            $this->assertEquals('The resource owner or authorization server denied the request.', $e->getMessage());
            $this->assertEquals('Missing "Authorization" header', $e->getHint());
        }
    }

    public function testValidateAuthorizationWithInvalidHeaderValue(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->never())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2023-04-28 15:59:59'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'invalid-header']
        );
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator($this->accessTokenRepository, $this->encryptionKey);
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Ciphertext has invalid hex encoding.');
        $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
    }

    public function testValidateAuthorizationWithInvalidToken(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->never())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2023-04-28 15:59:59'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer invalid-token']
        );
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator($this->accessTokenRepository, $this->encryptionKey);
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Ciphertext has invalid hex encoding.');

        $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
    }

    public function testValidateAuthorizationWithInvalidEncryptionKey(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->never())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2023-04-28 15:59:59'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer def502000c8aa98c7a00aed824a3b6003762930f43079e209179c3fcec118d042a12af3b7d7f5c3a523e2da51342b8437d1f71346372250680679aae9292e22786b2ef324fd2f92db35acb8030f919a82c4b61e051fcd1eba386b4a97da572a3dfa270605541d751301709cf3299afac0e53e288f3325123ebb950a89649968bd2dc461b64e7c6c8e79f6203cb0124fe03bdc17d00999d678ede85d6af14db97a0ad0089']
        );
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator(
            $this->accessTokenRepository,
            'pPE/4/7tPIsha317zoTA+945UjJzdRFbCBtYUNrR7x8='
        ); // Use invalid encryption key except $this->encryptionKey

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Integrity check failed.');

        $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
    }

    public function testValidateAuthorizationWithRevokedToken(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->never())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2023-04-28 15:59:59'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer def502000c8aa98c7a00aed824a3b6003762930f43079e209179c3fcec118d042a12af3b7d7f5c3a523e2da51342b8437d1f71346372250680679aae9292e22786b2ef324fd2f92db35acb8030f919a82c4b61e051fcd1eba386b4a97da572a3dfa270605541d751301709cf3299afac0e53e288f3325123ebb950a89649968bd2dc461b64e7c6c8e79f6203cb0124fe03bdc17d00999d678ede85d6af14db97a0ad0089']
        );
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator($this->accessTokenRepository, $this->encryptionKey);
        try {
            $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
            $this->fail();
        } catch (OAuthServerException $e) {
            $this->assertEquals('The resource owner or authorization server denied the request.', $e->getMessage());
            $this->assertEquals('Access token has been revoked', $e->getHint());
        }
    }

    /**
     * Valid token value in request header, but after decryption token id is not exist in the DB
     */
    public function testValidateAuthorizationWithNonExistingToken(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->never())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2024-05-06 11:44:54'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer def5020050af2f8fb14e8cee222c2e7a98a335f2cf4906d75ac7da9d591ad23604101d2df1fe7ff91eb5039e490371c8ea81a6f2e2ddc1ecd69cc4afb94f455d166cf73c36be559918d11120be0e39860ce90055bb62176df8cdf107fe52d6fbd45caff14daed9be26be493cc7d2815c8b3ac563a7c7d9f6bdf884d1cbf7fe54f291407255743115ab6ce37880070ec8710ea485ed8c31879b374c35290ca5d41f3dd9d3']
        );
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator($this->accessTokenRepository, $this->encryptionKey);
        try {
            $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
            $this->fail();
        } catch (OAuthServerException $e) {
            $this->assertEquals('The resource owner or authorization server denied the request.', $e->getMessage());
            $this->assertEquals('Access token could not be verified', $e->getHint());
        }
    }

    public function testValidateAuthorizationWithExpiredToken(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2023-05-06 11:44:55'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer def5020037757aeb15535f4b3defb02a6345565e08dffdac5851bd0959ee4bc34b257723a77be408fba5d3045fa5593db074b6b9db30b538089ee98895d810521c8527e9bc822a412303a8c52f4ad9cecebd87c6dab01b78a51836e09cd6deccb9acacca354bfbab63c5d8da05f33b3665b42e71ddc797d3c7a111ef1bf0e9e4ff02fe9a118a8790f115073e690fb63dfb64800a652f9df7992d7f60d630b3eb17024a94']
        );
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator($this->accessTokenRepository, $this->encryptionKey);
        try {
            $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
            $this->fail();
        } catch (OAuthServerException $e) {
            $this->assertEquals('The resource owner or authorization server denied the request.', $e->getMessage());
            $this->assertEquals('The token is expired', $e->getHint());
        }
    }

    public function testValidateAuthorizationWithExpiredTokenAfterOneYear(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2024-05-06 11:44:54'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer def5020037757aeb15535f4b3defb02a6345565e08dffdac5851bd0959ee4bc34b257723a77be408fba5d3045fa5593db074b6b9db30b538089ee98895d810521c8527e9bc822a412303a8c52f4ad9cecebd87c6dab01b78a51836e09cd6deccb9acacca354bfbab63c5d8da05f33b3665b42e71ddc797d3c7a111ef1bf0e9e4ff02fe9a118a8790f115073e690fb63dfb64800a652f9df7992d7f60d630b3eb17024a94']
        );
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator($this->accessTokenRepository, $this->encryptionKey);
        try {
            $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
            $this->fail();
        } catch (OAuthServerException $e) {
            $this->assertEquals('The resource owner or authorization server denied the request.', $e->getMessage());
            $this->assertEquals('The token is expired', $e->getHint());
        }
    }

    public function testValidateAuthorizationWithExpiredAndCurrentTimeSame(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2023-05-06 11:44:54'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer def5020037757aeb15535f4b3defb02a6345565e08dffdac5851bd0959ee4bc34b257723a77be408fba5d3045fa5593db074b6b9db30b538089ee98895d810521c8527e9bc822a412303a8c52f4ad9cecebd87c6dab01b78a51836e09cd6deccb9acacca354bfbab63c5d8da05f33b3665b42e71ddc797d3c7a111ef1bf0e9e4ff02fe9a118a8790f115073e690fb63dfb64800a652f9df7992d7f60d630b3eb17024a94']
        );
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator($this->accessTokenRepository, $this->encryptionKey);
        $request = $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
        $accessToken = $request->getAttribute('_oauth2_access_token');
        $this->assertInstanceOf(OAuthAccessToken::class, $accessToken);
        $this->assertEquals(
            'ddd1d9b88fac8d670b3024dbf6417b5eb4dbcccea9eb51520595772615d33f9f540ccc3c4d3a51c6',
            $accessToken->getAccessToken(),
        );
        $this->assertEquals(5, $accessToken->getId());
        $this->assertEquals(1, $accessToken->getClientId());
        $this->assertEquals(5, $accessToken->getUserId());
        $this->assertEquals('2023-05-06 11:44:54', $accessToken->getExpiryDateTime()->format('Y-m-d H:i:s'));
    }

    public function testValidateAuthorization(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2023-04-28 15:59:59'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $request = Request::create(
            '/api/v2/pim/employees',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer def5020037757aeb15535f4b3defb02a6345565e08dffdac5851bd0959ee4bc34b257723a77be408fba5d3045fa5593db074b6b9db30b538089ee98895d810521c8527e9bc822a412303a8c52f4ad9cecebd87c6dab01b78a51836e09cd6deccb9acacca354bfbab63c5d8da05f33b3665b42e71ddc797d3c7a111ef1bf0e9e4ff02fe9a118a8790f115073e690fb63dfb64800a652f9df7992d7f60d630b3eb17024a94']
        );
        $psrHttpFactory = new PsrHttpFactoryHelper();

        $tokenValidator = new BearerTokenValidator($this->accessTokenRepository, $this->encryptionKey);
        $request = $tokenValidator->validateAuthorization($psrHttpFactory->createPsr7Request($request));
        $accessToken = $request->getAttribute('_oauth2_access_token');
        $this->assertInstanceOf(OAuthAccessToken::class, $accessToken);
        $this->assertEquals(
            'ddd1d9b88fac8d670b3024dbf6417b5eb4dbcccea9eb51520595772615d33f9f540ccc3c4d3a51c6',
            $accessToken->getAccessToken(),
        );
        $this->assertEquals(5, $accessToken->getId());
        $this->assertEquals(1, $accessToken->getClientId());
        $this->assertEquals(5, $accessToken->getUserId());
        $this->assertEquals('2023-05-06 11:44:54', $accessToken->getExpiryDateTime()->format('Y-m-d H:i:s'));
    }
}
