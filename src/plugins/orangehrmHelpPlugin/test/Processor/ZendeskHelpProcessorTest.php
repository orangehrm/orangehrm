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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Tests\Help\Processor;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OrangeHRM\Config\Config;
use OrangeHRM\Help\Processor\ZendeskHelpProcessor;
use OrangeHRM\Help\Service\HelpConfigService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Help
 * @group Processor
 */
class ZendeskHelpProcessorTest extends KernelTestCase
{
    private ZendeskHelpProcessor $zendeskHelpProcessor;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->zendeskHelpProcessor = new ZendeskHelpProcessor();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmHelpPlugin/test/fixtures/HelpServiceTest.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetHelpConfigService(): void
    {
        $helpConfigService = $this->zendeskHelpProcessor->getHelpConfigService();
        $this->assertInstanceOf(HelpConfigService::class, $helpConfigService);
    }

    public function testGetHttpClient(): void
    {
        $httpClient = $this->zendeskHelpProcessor->getHttpClient();
        $this->assertInstanceOf(Client::class, $httpClient);
    }

    public function testGetBaseUrl(): void
    {
        $baseUrl = $this->zendeskHelpProcessor->getBaseUrl();
        $this->assertEquals("https://starterhelp.orangehrm.com", $baseUrl);
    }

    public function testGetSearchUrl(): void
    {
        $searchUrl = $this->zendeskHelpProcessor->getSearchUrl("admin_viewSystemUsers");
        $this->assertEquals(
            "https://starterhelp.orangehrm.com/api/v2/help_center/articles/search.json?label_names=admin_viewSystemUsers",
            $searchUrl
        );
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

        $redirectUrl = $zendeskHelpProcessorMock->getRedirectUrl('admin_viewSystemUsers');
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

        $redirectUrl = $zendeskHelpProcessorMock->getRedirectUrl('admin_viewSystemUsers');
        $this->assertEquals(
            "https://starterhelp.orangehrm.com/hc/en-us",
            $redirectUrl
        );
    }

    public function testGetRedirectUrl3(): void
    {
        $zendeskHelpProcessorMock = $this->getMockBuilder(ZendeskHelpProcessor::class)
            ->onlyMethods(['getHttpClient'])
            ->getMock();

        $mockHandler = new MockHandler([
            new RequestException('Error', new Request('GET', 'test'))
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $zendeskHelpProcessorMock->expects($this->once())
            ->method('getHttpClient')
            ->willReturn($client);

        $redirectUrl = $zendeskHelpProcessorMock->getRedirectUrl('admin_viewSystemUsers');
        $this->assertEquals(
            "https://starterhelp.orangehrm.com/hc/en-us",
            $redirectUrl
        );
    }

    public function testGetDefaultRedirectUrl(): void
    {
        $defaultRedirectUrl = $this->zendeskHelpProcessor->getDefaultRedirectUrl();
        $this->assertEquals(
            "https://starterhelp.orangehrm.com/hc/en-us",
            $defaultRedirectUrl
        );
    }
}
