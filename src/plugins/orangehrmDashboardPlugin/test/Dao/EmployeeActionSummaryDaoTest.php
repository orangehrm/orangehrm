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

namespace OrangeHRM\Tests\Dashboard\Dao;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Dashboard\Dao\EmployeeActionSummaryDao;
use OrangeHRM\Dashboard\Dto\ActionableReviewSearchFilterParams;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Dashboard
 * @group Dao
 */
class EmployeeActionSummaryDaoTest extends KernelTestCase
{
    protected string $fixture;
    protected EmployeeActionSummaryDao $employeeActionSummaryDao;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->employeeActionSummaryDao = new EmployeeActionSummaryDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmDashboardPlugin/test/fixtures/EmployeeActionSummary.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetActionableScheduledInterviewCount(): void
    {
        $candidateIds = [23, 24, 25, 26, 27];

        $count = $this->employeeActionSummaryDao->getActionableScheduledInterviewCount($candidateIds);
        $this->assertEquals(4, $count);

        $candidateIds = [25, 26, 27];

        $count = $this->employeeActionSummaryDao->getActionableScheduledInterviewCount($candidateIds);
        $this->assertEquals(2, $count);

        $candidateIds = [28, 29];

        $count = $this->employeeActionSummaryDao->getActionableScheduledInterviewCount($candidateIds);
        $this->assertEquals(0, $count);
    }

    public function testGetActionableReviewCount(): void
    {
        $actionableReviewSearchFilterParams = new ActionableReviewSearchFilterParams();
        $actionableReviewSearchFilterParams->setEmpNumber(2);
        $actionableReviewSearchFilterParams->setReviewerEmpNumber(2);
        $actionableReviewSearchFilterParams->setActionableStatuses(
            [
                WorkflowStateMachine::REVIEW_ACTIVATE,
                WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE
            ]
        );
        $count = $this->employeeActionSummaryDao->getPendingAppraisalReviewCount($actionableReviewSearchFilterParams);
        $this->assertEquals(0, $count);

        $actionableReviewSearchFilterParams->setEmpNumber(5);
        $actionableReviewSearchFilterParams->setReviewerEmpNumber(5);
        $count = $this->employeeActionSummaryDao->getPendingAppraisalReviewCount($actionableReviewSearchFilterParams);
        $this->assertEquals(1, $count);

        $actionableReviewSearchFilterParams->setEmpNumber(6);
        $actionableReviewSearchFilterParams->setReviewerEmpNumber(6);
        $count = $this->employeeActionSummaryDao->getPendingAppraisalReviewCount($actionableReviewSearchFilterParams);
        $this->assertEquals(0, $count);
    }
}
