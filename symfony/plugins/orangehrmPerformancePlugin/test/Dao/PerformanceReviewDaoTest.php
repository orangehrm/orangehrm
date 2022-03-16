<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerformanceReviewDaoTest
 *
 * @author nadeera
 */

namespace OrangeHRM\Performance\test\Dao;

use Exception;
use OrangeHRM\Performance\Dao\PerformanceReviewDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group performance
 */
class PerformanceReviewDaoTest extends TestCase
{
    private $performanceReviewDao;
    protected $fixture;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->performanceReviewDao = new PerformanceReviewDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/PerformanceReviews.yml';
        TestDataService::populate($this->fixture);
    }
}
