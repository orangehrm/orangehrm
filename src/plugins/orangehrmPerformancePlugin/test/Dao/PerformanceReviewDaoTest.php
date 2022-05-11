<?php

namespace OrangeHRM\Tests\Performance\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Performance\Dao\PerformanceReviewDao;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class PerformanceReviewDaoTest extends KernelTestCase
{
    private PerformanceReviewDao $performanceReviewDao;
    protected $fixture;

    protected function setUp(): void
    {
        $this->performanceReviewDao = new PerformanceReviewDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/PerformanceReview.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetPerformanceReviewList()
    {
        $performanceReviewSearchFilterParams = new PerformanceReviewSearchFilterParams();
        $performanceReviewSearchFilterParams->setEmpNumber(2);
        $result = $this->performanceReviewDao->getPerformanceReviewList($performanceReviewSearchFilterParams);
        $this->assertCount(2, $result);
    }
}
