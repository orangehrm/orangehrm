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

namespace OrangeHRM\Tests\OAuth\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Api\OAuthClientAPI;
use OrangeHRM\OAuth\Dao\OAuthClientDao;
use OrangeHRM\OAuth\Service\OAuthService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group OAuth
 * @group APIv2
 */
class OAuthClientAPITest extends EndpointTestCase
{
    public function testGetOAuthService(): void
    {
        $api = new OAuthClientAPI($this->getRequest());
        $this->assertTrue($api->getOAuthService() instanceof OAuthService);
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new OAuthClientAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [],
                $rules
            )
        );

        $this->expectInvalidParamException();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 100],
                $rules
            )
        );
    }

    public function testGetAll(): void
    {
        $oAuthClientDao = $this->getMockBuilder(OAuthClientDao::class)
            ->onlyMethods(['getOAuthClients', 'getOAuthClientsCount'])
            ->getMock();

        $oauthClient1 = new OAuthClient();
        $oauthClient1->setClientId('TestOAuth1');
        $oauthClient1->setClientSecret('TestOAuthSecret');
        $oauthClient1->setRedirectUri('https://facebook.com');
        $oauthClient1->setGrantTypes('password');
        $oauthClient1->setScope('user');

        $oauthClient2 = new OAuthClient();
        $oauthClient2->setClientId('TestOAuth2');
        $oauthClient2->setClientSecret('TestOAuthSecret');
        $oauthClient2->setRedirectUri('https://facebook.com');
        $oauthClient2->setGrantTypes('password');
        $oauthClient2->setScope('user');

        $oAuthClientDao->expects($this->exactly(1))
            ->method('getOAuthClients')
            ->willReturn([$oauthClient1, $oauthClient2]);
        $oAuthClientDao->expects($this->exactly(1))
            ->method('getOAuthClientsCount')
            ->willReturn(2);
        $oAuthService = $this->getMockBuilder(OAuthService::class)
            ->onlyMethods(['getOAuthClientDao'])
            ->getMock();
        $oAuthService->expects($this->exactly(2))
            ->method('getOAuthClientDao')
            ->willReturn($oAuthClientDao);

        /** @var MockObject&OAuthClientAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            OAuthClientAPI::class,
            []
        )->onlyMethods(['getOAuthService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getOAuthService')
            ->will($this->returnValue($oAuthService));

        $result = $api->getAll();

        $this->assertEquals(
            [
                [
                    "clientId" => 'TestOAuth1',
                    "clientSecret" => 'TestOAuthSecret',
                    "redirectUri" => 'https://facebook.com',
                ],
                [
                    "clientId" => 'TestOAuth2',
                    "clientSecret" => 'TestOAuthSecret',
                    "redirectUri" => 'https://facebook.com',
                ],
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "total" => 2
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new OAuthClientAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    OAuthClientAPI::PARAMETER_CLIENT_ID => 'Test',
                    OAuthClientAPI::PARAMETER_CLIENT_SECRET => 'TestSecret',
                    OAuthClientAPI::PARAMETER_REDIRECT_URI => 'https://facebook.com',
                ],
                $rules
            )
        );
    }

    public function testCreate()
    {
        $oAuthService = $this->getMockBuilder(OAuthService::class)
            ->onlyMethods(['saveOAuthClient'])
            ->getMock();
        $oAuthService->expects($this->once())
            ->method('saveOAuthClient')
            ->will(
                $this->returnCallback(
                    function (OAuthClient $authClient) {
                        $authClient->setClientId('Test');
                        $authClient->setClientSecret('TEST');
                        $authClient->setRedirectUri('');
                        return $authClient;
                    }
                )
            );

        /** @var MockObject&OAuthClientAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            OAuthClientAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    OAuthClientAPI::PARAMETER_CLIENT_ID => 'Test',
                    OAuthClientAPI::PARAMETER_CLIENT_SECRET => 'TEST',
                    OAuthClientAPI::PARAMETER_REDIRECT_URI => '',
                ]
            ]
        )->onlyMethods(['getOAuthService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getOAuthService')
            ->will($this->returnValue($oAuthService));

        $result = $api->create();
        $this->assertEquals(
            [
                "clientId" => 'Test',
                "clientSecret" => 'TEST',
                "redirectUri" => ''
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new OAuthClientAPI($this->getRequest());
        $rules = $api->getValidationRuleForDelete();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_IDS => ['client1'],
                ],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $oAuthService = $this->getMockBuilder(OAuthService::class)
            ->onlyMethods(['deleteOAuthClients'])
            ->getMock();
        $oAuthService->expects($this->exactly(1))
            ->method('deleteOAuthClients')
            ->with(['client1'])
            ->willReturn(1);

        /** @var MockObject&OAuthClientAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            OAuthClientAPI::class,
            [

                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => ['client1'],
                ]
            ]
        )->onlyMethods(['getOAuthService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getOAuthService')
            ->will($this->returnValue($oAuthService));

        $result = $api->delete();
        $this->assertEquals(
            [
                'client1'
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new OAuthClientAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [OAuthClientAPI::PARAMETER_CLIENT_ID => 'client1'],
                $rules
            )
        );
    }

    public function testGetOne(): void
    {
        $oAuthClient = new OAuthClient();
        $oAuthClient->setClientId('client1');
        $oAuthClient->setClientSecret('clientsecret');
        $oAuthClient->setRedirectUri('');
        $oAuthClient->setGrantTypes('password');
        $oAuthClient->setScope('user');

        $oAuthService = $this->getMockBuilder(OAuthService::class)
            ->onlyMethods(['getOAuthClientByClientId'])
            ->getMock();
        $oAuthService->expects($this->exactly(1))
            ->method('getOAuthClientByClientId')
            ->with('client1')
            ->willReturn($oAuthClient);

        /** @var MockObject&OAuthClientAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            OAuthClientAPI::class,
            [
                RequestParams::PARAM_TYPE_QUERY => [
                    OAuthClientAPI::PARAMETER_CLIENT_ID => 'client1'
                ]
            ]
        )->onlyMethods(['getOAuthService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getOAuthService')
            ->will($this->returnValue($oAuthService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "clientId" => 'client1',
                "clientSecret" => 'clientsecret',
                "redirectUri" => '',
            ],
            $result->normalize()
        );
    }

    public function testGetOneWhenRecordNotFound(): void
    {
        $oAuthService = $this->getMockBuilder(OAuthService::class)
            ->onlyMethods(['getOAuthClientByClientId'])
            ->getMock();
        $oAuthService->expects($this->exactly(1))
            ->method('getOAuthClientByClientId')
            ->with('client2')
            ->willReturn(null);

        /** @var MockObject&OAuthClientAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            OAuthClientAPI::class,
            [
                RequestParams::PARAM_TYPE_QUERY => [
                    OAuthClientAPI::PARAMETER_CLIENT_ID => 'client2'
                ]
            ]
        )->onlyMethods(['getOAuthService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getOAuthService')
            ->will($this->returnValue($oAuthService));

        $this->expectRecordNotFoundException();
        $api->getOne();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new OAuthClientAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    OAuthClientAPI::PARAMETER_CLIENT_ID => 'client2',
                    OAuthClientAPI::PARAMETER_CLIENT_SECRET => 'newsecret',
                    OAuthClientAPI::PARAMETER_REDIRECT_URI => 'https://facebook.com',
                ],
                $rules
            )
        );
    }

    public function testUpdate()
    {
        $oAuthService = $this->getMockBuilder(OAuthService::class)
            ->onlyMethods(['saveOAuthClient', 'getOAuthClientByClientId'])
            ->getMock();
        $oAuthService->expects($this->once())
            ->method('saveOAuthClient')
            ->will(
                $this->returnCallback(
                    function (OAuthClient $authClient) {
                        $authClient->setClientId('TestNew');
                        $authClient->setClientSecret('TESTNEW');
                        $authClient->setRedirectUri('');
                        return $authClient;
                    }
                )
            );

        $existingOAuthClient = new OAuthClient();
        $existingOAuthClient->setClientId('Test');
        $existingOAuthClient->setClientSecret('TEST');
        $existingOAuthClient->setRedirectUri('');
        $existingOAuthClient->setGrantTypes('password');
        $existingOAuthClient->setScope('user');

        $oAuthService->expects($this->exactly(1))
            ->method('getOAuthClientByClientId')
            ->with('Test')
            ->willReturn($existingOAuthClient);


        /** @var MockObject&OAuthClientAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            OAuthClientAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    OAuthClientAPI::PARAMETER_CLIENT_ID => 'TestNew',
                    OAuthClientAPI::PARAMETER_CLIENT_SECRET => 'TESTNEW',
                    OAuthClientAPI::PARAMETER_REDIRECT_URI => '',
                ],
                RequestParams::PARAM_TYPE_QUERY => [
                    OAuthClientAPI::PARAMETER_CLIENT_ID => 'Test',
                ]
            ]
        )->onlyMethods(['getOAuthService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getOAuthService')
            ->will($this->returnValue($oAuthService));

        $result = $api->update();
        $this->assertEquals(
            [
                "clientId" => 'TestNew',
                "clientSecret" => 'TESTNEW',
                "redirectUri" => ''
            ],
            $result->normalize()
        );
    }
}
