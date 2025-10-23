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

namespace OrangeHRM\Tests\Help\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use OrangeHRM\Config\Config;
use OrangeHRM\Help\Processor\ZendeskHelpProcessor;
use OrangeHRM\Help\Service\HelpConfigService;
use OrangeHRM\Help\Service\HelpService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Help
 * @group Service
 */
class HelpServiceTest extends KernelTestCase
{
    private HelpService $helpService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->helpService = new HelpService();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmHelpPlugin/test/fixtures/HelpServiceTest.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetHelpConfigService(): void
    {
        $helpConfigService = $this->helpService->getHelpConfigService();
        $this->assertInstanceOf(HelpConfigService::class, $helpConfigService);
    }

    public function testGetZendeskHelpProcessor(): void
    {
        $helpProcessor = $this->helpService->getHelpProcessor();
        $this->assertInstanceOf(ZendeskHelpProcessor::class, $helpProcessor);
    }

    public function testGetRedirectUrl(): void
    {
        $zendeskHelpProcessorMock = $this->getMockBuilder(ZendeskHelpProcessor::class)
            ->onlyMethods(['getHttpClient'])
            ->getMock();

        $mockHandler = new MockHandler([
            new Response(
                200,
                [],
                '{"count":1,"next_page":null,"page":1,"page_count":1,"per_page":25,"previous_page":null,"results":[{"id":360018588480,"url":"https://opensourcehelp.zendesk.com/api/v2/help_center/en-us/articles/360018588480.json","html_url":"https://starterhelp.orangehrm.com/hc/en-us/articles/360018588480-How-to-Add-a-User-Account","author_id":375356643480,"comments_disabled":false,"draft":false,"promoted":false,"position":1,"vote_sum":0,"vote_count":0,"section_id":360005148199,"created_at":"2020-12-31T05:15:33Z","updated_at":"2021-11-16T08:00:26Z","name":"How to Add a User Account","title":"How to Add a User Account","source_locale":"en-us","locale":"en-us","outdated":false,"outdated_locales":[],"edited_at":"2021-01-01T07:40:09Z","user_segment_id":null,"permission_group_id":1279059,"content_tag_ids":[],"label_names":["admin_viewSystemUsers","admin_saveSystemUser"],"snippet":"An organization may have a tool in place to store all employee data but may lack the ability to assign role-specific functions","result_type":"article"}]}'
            )
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $zendeskHelpProcessorMock->expects($this->once())
            ->method('getHttpClient')
            ->willReturn($client);

        $helpServiceMock = $this->getMockBuilder(HelpService::class)
            ->onlyMethods(['getHelpProcessor'])
            ->getMock();

        $helpServiceMock->expects($this->once())
            ->method('getHelpProcessor')
            ->willReturn($zendeskHelpProcessorMock);

        $redirectUrl = $helpServiceMock->getRedirectUrl('admin_viewSystemUsers');
        $this->assertEquals(
            "https://starterhelp.orangehrm.com/hc/en-us/articles/360018588480-How-to-Add-a-User-Account",
            $redirectUrl
        );
    }

    public function testGetRedirectUrl2(): void
    {
        $zendeskHelpProcessorMock = $this->getMockBuilder(ZendeskHelpProcessor::class)
            ->onlyMethods(['getHttpClient'])
            ->getMock();

        $mockHandler = new MockHandler([
            new Response(
                200,
                [],
                '{"count":0,"next_page":null,"page":1,"page_count":0,"per_page":25,"previous_page":null,"results":[]}'
            )
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $zendeskHelpProcessorMock->expects($this->once())
            ->method('getHttpClient')
            ->willReturn($client);

        $helpServiceMock = $this->getMockBuilder(HelpService::class)
            ->onlyMethods(['getHelpProcessor'])
            ->getMock();

        $helpServiceMock->expects($this->once())
            ->method('getHelpProcessor')
            ->willReturn($zendeskHelpProcessorMock);

        $redirectUrl = $helpServiceMock->getRedirectUrl('admin_viewSystemUsers');
        $this->assertEquals(
            "https://starterhelp.orangehrm.com/hc/en-us",
            $redirectUrl
        );
    }

    public function testGetDefaultRedirectUrl(): void
    {
        $defaultRedirectUrl = $this->helpService->getDefaultRedirectUrl();
        $this->assertEquals(
            "https://starterhelp.orangehrm.com/hc/en-us",
            $defaultRedirectUrl
        );
    }

    public function testIsValidUrl(): void
    {
        // Check whether help.url in config is valid
        $valid = $this->helpService->isValidUrl();
        $this->assertTrue($valid);
    }

    public function testIsValidUrl2(): void
    {
        $helpConfigServiceMock = $this->getMockBuilder(HelpConfigService::class)
            ->onlyMethods(['getBaseHelpUrl'])
            ->getMock();

        $helpConfigServiceMock->expects($this->once())
            ->method('getBaseHelpUrl')
            ->willReturn('abcdefg');

        $helpServiceMock = $this->getMockBuilder(HelpService::class)
            ->onlyMethods(['getHelpConfigService'])
            ->disableOriginalConstructor()
            ->getMock();

        $helpServiceMock->expects($this->once())
            ->method('getHelpConfigService')
            ->willReturn($helpConfigServiceMock);

        $valid = $helpServiceMock->isValidUrl();
        $this->assertFalse($valid);
    }
}
