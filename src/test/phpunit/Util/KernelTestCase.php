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

namespace OrangeHRM\Tests\Util;

use LogicException;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NumberHelperService;
use OrangeHRM\Core\Service\TextHelperService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Framework;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Service\I18NHelper;
use OrangeHRM\Performance\Service\PerformanceTrackerService;
use OrangeHRM\Recruitment\Service\CandidateService;
use OrangeHRM\Recruitment\Service\VacancyService;
use OrangeHRM\Tests\Util\Database\DoctrineService;
use OrangeHRM\Time\Service\ProjectService;

abstract class KernelTestCase extends TestCase
{
    use ServiceContainerTrait;

    /**
     * Value of this key should be bool, default: true
     */
    public const OPTIONS_WITH_HELPER_SERVICES = 'withHelperServices';
    public const OPTIONS_WITH_BASE_SERVICES = 'withBaseServices';

    private array $options = [
        self::OPTIONS_WITH_HELPER_SERVICES => true,
        self::OPTIONS_WITH_BASE_SERVICES => true,
    ];

    protected function tearDown(): void
    {
        $this->getEntityManager()->clear();
        $this->createKernel();
    }

    /**
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @return Request
     */
    protected function getHttpRequest(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ): Request {
        return new Request($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * @return Framework
     */
    protected function createKernel(): Framework
    {
        foreach ($this->getContainer()->getServiceIds() as $serviceId) {
            if ($serviceId === 'service_container') {
                continue;
            }
            $this->getContainer()->set($serviceId, null);
        }
        $this->getContainer()->set(Services::DOCTRINE, DoctrineService::getEntityManager());

        $this->setHelperServices();
        $this->setBaseServices();

        return $this->getMockBuilder(Framework::class)
            ->onlyMethods(['handle'])
            ->setConstructorArgs(['test', true])
            ->getMock();
    }

    /**
     * @param array $services
     * @return Framework
     */
    protected function createKernelWithMockServices(array $services = []): Framework
    {
        $kernel = $this->createKernel();

        foreach ($services as $id => $service) {
            $this->getContainer()->set($id, $service);
        }
        return $kernel;
    }

    /**
     * @param array $options
     */
    protected function updateOptions(array $options)
    {
        $this->options = array_replace($this->options, $options);
    }

    private function setHelperServices(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturnCallback(function () {
                throw new LogicException('Please mock ' . DateTimeHelperService::class . '::getNow');
            });
        $i18nHelper = $this->getMockBuilder(I18NHelper::class)
            ->onlyMethods(['transBySource'])
            ->getMock();
        $i18nHelper->method('transBySource')
            ->willReturnCallback(fn ($string) => $string);
        if (isset($this->options[self::OPTIONS_WITH_HELPER_SERVICES]) && $this->options[self::OPTIONS_WITH_HELPER_SERVICES]) {
            $this->getContainer()->set(Services::DATETIME_HELPER_SERVICE, $dateTimeHelper);
            $this->getContainer()->set(Services::TEXT_HELPER_SERVICE, new TextHelperService());
            $this->getContainer()->set(Services::NUMBER_HELPER_SERVICE, new NumberHelperService());
            $this->getContainer()->set(Services::CLASS_HELPER, new ClassHelper());
            $this->getContainer()->set(Services::I18N_HELPER, $i18nHelper);
        }
    }

    private function setBaseServices(): void
    {
        if (isset($this->options[self::OPTIONS_WITH_BASE_SERVICES]) && $this->options[self::OPTIONS_WITH_BASE_SERVICES]) {
            $this->getContainer()->set(Services::PROJECT_SERVICE, new ProjectService());
            $this->getContainer()->set(Services::PERFORMANCE_TRACKER_SERVICE, new PerformanceTrackerService());
            $this->getContainer()->set(Services::VACANCY_SERVICE, new VacancyService());
            $this->getContainer()->set(Services::CANDIDATE_SERVICE, new CandidateService());
        }
    }
}
