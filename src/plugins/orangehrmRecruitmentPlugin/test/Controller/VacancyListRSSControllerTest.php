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

namespace OrangeHRM\Tests\Recruitment\Controller;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\Recruitment\Controller\PublicController\VacancyListRSSController;
use OrangeHRM\Recruitment\Service\VacancyService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Recruitment
 * @group Controller
 */
class VacancyListRSSControllerTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmRecruitmentPlugin/test/fixtures/VacancyRSSFeed.yaml';
        TestDataService::populate($fixture);
    }

    public function testGenerateRSSFeed(): void
    {
        $urlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->onlyMethods(['generate'])
            ->disableOriginalConstructor()
            ->getMock();

        $urlGenerator->expects($this->exactly(6))
            ->method('generate')
            ->willReturnMap(
                [
                    [
                        'recruitment_rss_feed',
                        'http://localhost/orangeHrm/orangehrm/web/index.php/recruitmentApply/jobs.rss'
                    ],
                    [
                        'recruitment_view_vacancy_list',
                        'http://localhost/orangeHrm/orangehrm/web/index.php/recruitmentApply/jobs.html'
                    ]
                ]
            );

        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturnCallback(fn () => new \DateTime('2020-10-10'));

        $this->createKernelWithMockServices([
            Services::VACANCY_SERVICE => new VacancyService(),
            Services::URL_GENERATOR => $urlGenerator,
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::CONFIG_SERVICE => new ConfigService()
        ]);
        $controller = new VacancyListRSSController();
        $response = $controller->handle();
        $xml = simplexml_load_string($response->getContent(), "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $result = json_decode($json, true);

        $this->assertEquals('text/xml', $response->headers->get('content-type'));
        $this->assertEquals('2.0', $result['@attributes']['version']);
        $this->assertEquals('Active Job Vacancies', $result['channel']['title']);
        $this->assertCount(4, $result['channel']['item']);
        $this->assertEquals('Assistant Technical Supervisor', $result['channel']['item'][0]['title']);
        $this->assertEquals('Oversees technical assistant', $result['channel']['item'][0]['description']);
        $this->assertEquals('Sat, 10 Oct 2020 03:48:00 +0000', $result['channel']['item'][0]['pubDate']);
        $this->assertEquals('Part-Time Technical Assistant', $result['channel']['item'][3]['title']);
        $this->assertEquals([], $result['channel']['item'][3]['description']);
        $this->assertEquals('Thu, 08 Oct 2020 03:48:00 +0000', $result['channel']['item'][3]['pubDate']);
    }
}
