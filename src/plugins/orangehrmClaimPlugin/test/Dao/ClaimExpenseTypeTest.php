<?php

namespace OrangeHRM\Claim\test\Dao;

use OrangeHRM\Claim\Dao\ClaimDao;
use OrangeHRM\Claim\Dto\ClaimExpenseTypeSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\ExpenseType;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class ClaimExpenseTypeTest extends KernelTestCase
{
    use EntityManagerHelperTrait;
    private ClaimDao $claimDao;

    protected function setUp(): void
    {
        $this->claimDao = new ClaimDao();
        $expenseTypeFixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmClaimPlugin/test/fixtures/ExpenseType.yaml';
        TestDataService::populate($expenseTypeFixture);
    }
    public function testGetExpenseTypeList(): void
    {
        $expenseTypeSearchFilterParams = new ClaimExpenseTypeSearchFilterParams();
        $expenseTypeSearchFilterParams->setName(null);
        $expenseTypeSearchFilterParams->setStatus(null);
        $expenseTypeSearchFilterParams->setId(null);
        $result = $this->claimDao->getExpenseTypeList($expenseTypeSearchFilterParams);
        $this->assertEquals("medical", $result[0]->getName());
        $this->assertCount("4", $result);
    }
}
