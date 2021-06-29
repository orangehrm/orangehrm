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

namespace OrangeHRM\Tests\Pim\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeLanguage;
use OrangeHRM\Entity\Language;
use OrangeHRM\Pim\Dao\EmployeeLanguageDao;
use OrangeHRM\Pim\Dto\EmployeeAllowedLanguageSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeLanguagesSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeLanguageDaoTest extends TestCase
{
    private EmployeeLanguageDao $employeeLanguageDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeeLanguageDao = new EmployeeLanguageDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeLanguageDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveEmployeeLanguage(): void
    {
        $employee = $this->getEntityReference(Employee::class, 3);
        $language = $this->getEntityReference(Language::class, 1);
        $empLanguage = new EmployeeLanguage();
        $empLanguage->setEmployee($employee);
        $empLanguage->setLanguage($language);
        $empLanguage->setFluency(1);
        $empLanguage->setCompetency(1);
        $empLanguage->setComment('Test');

        $this->employeeLanguageDao->saveEmployeeLanguage($empLanguage);

        /** @var EmployeeLanguage $resultEmpLanguage */
        $resultEmpLanguage = $this->getEntityManager()->getRepository(EmployeeLanguage::class)->findOneBy(
            [
                'employee' => 3,
                'language' => 1,
                'fluency' => 1,
            ]
        );
        $this->assertEquals('Tyler', $resultEmpLanguage->getEmployee()->getFirstName());
        $this->assertEquals('English', $resultEmpLanguage->getLanguage()->getName());
        $this->assertEquals('Writing', $resultEmpLanguage->getDecorator()->getFluency());
        $this->assertEquals('Poor', $resultEmpLanguage->getDecorator()->getCompetency());
        $this->assertEquals('Test', $resultEmpLanguage->getComment());
    }

    public function testGetEmployeeLanguage(): void
    {
        $resultEmpLanguage = $this->employeeLanguageDao->getEmployeeLanguage(1, 2, 1);
        $this->assertEquals('Kayla', $resultEmpLanguage->getEmployee()->getFirstName());
        $this->assertEquals('Spanish', $resultEmpLanguage->getLanguage()->getName());
        $this->assertEquals('Writing', $resultEmpLanguage->getDecorator()->getFluency());
        $this->assertEquals('Basic', $resultEmpLanguage->getDecorator()->getCompetency());
        $this->assertEquals('comment2', $resultEmpLanguage->getComment());

        $resultEmpLanguage = $this->employeeLanguageDao->getEmployeeLanguage(100, 2, 1);
        $this->assertNull($resultEmpLanguage);

        $resultEmpLanguage = $this->employeeLanguageDao->getEmployeeLanguage(1, 200, 1);
        $this->assertNull($resultEmpLanguage);

        $resultEmpLanguage = $this->employeeLanguageDao->getEmployeeLanguage(1, 1, 1);
        $this->assertNull($resultEmpLanguage);
    }

    public function testSearchEmployeeLanguages(): void
    {
        $employeeLanguagesSearchFilterParams = new EmployeeLanguagesSearchFilterParams();
        $empLanguages = $this->employeeLanguageDao->getEmployeeLanguages($employeeLanguagesSearchFilterParams);
        $this->assertEmpty($empLanguages);

        $employeeLanguagesSearchFilterParams->setEmpNumber(1);
        $empLanguages = $this->employeeLanguageDao->getEmployeeLanguages($employeeLanguagesSearchFilterParams);
        $this->assertCount(2, $empLanguages);

        $resultEmpLanguage = $empLanguages[0];
        $this->assertEquals('Kayla', $resultEmpLanguage->getEmployee()->getFirstName());
        $this->assertEquals('English', $resultEmpLanguage->getLanguage()->getName());
    }

    public function testGetSearchEmployeeLanguagesCount(): void
    {
        $employeeLanguagesSearchFilterParams = new EmployeeLanguagesSearchFilterParams();
        $empLanguagesCount = $this->employeeLanguageDao->getEmployeeLanguagesCount(
            $employeeLanguagesSearchFilterParams
        );
        $this->assertEquals(0, $empLanguagesCount);

        $employeeLanguagesSearchFilterParams->setEmpNumber(1);
        $empLanguagesCount = $this->employeeLanguageDao->getEmployeeLanguagesCount(
            $employeeLanguagesSearchFilterParams
        );
        $this->assertEquals(2, $empLanguagesCount);

        $employeeLanguagesSearchFilterParams->setEmpNumber(2);
        $empLanguagesCount = $this->employeeLanguageDao->getEmployeeLanguagesCount(
            $employeeLanguagesSearchFilterParams
        );
        $this->assertEquals(1, $empLanguagesCount);
    }

    public function testGetEmployeeLanguagesWithLanguageIds(): void
    {
        $employeeLanguagesSearchFilterParams = new EmployeeLanguagesSearchFilterParams();
        $employeeLanguagesSearchFilterParams->setEmpNumber(1);
        $employeeLanguagesSearchFilterParams->setLanguageIds([1]);
        $empLanguages = $this->employeeLanguageDao->getEmployeeLanguages($employeeLanguagesSearchFilterParams);
        $this->assertCount(1, $empLanguages);

        $resultEmpLanguage = $empLanguages[0];
        $this->assertEquals('Kayla', $resultEmpLanguage->getEmployee()->getFirstName());
        $this->assertEquals('English', $resultEmpLanguage->getLanguage()->getName());

        $employeeLanguagesSearchFilterParams->setLanguageIds([100]);
        $empLanguages = $this->employeeLanguageDao->getEmployeeLanguages($employeeLanguagesSearchFilterParams);
        $this->assertCount(0, $empLanguages);

        $employeeLanguagesSearchFilterParams->setEmpNumber(1);
        $employeeLanguagesSearchFilterParams->setLanguageIds([1, 2]);
        $empLanguages = $this->employeeLanguageDao->getEmployeeLanguages($employeeLanguagesSearchFilterParams);
        $this->assertCount(2, $empLanguages);

        $resultEmpLanguage = $empLanguages[0];
        $this->assertEquals('Kayla', $resultEmpLanguage->getEmployee()->getFirstName());
        $this->assertEquals('English', $resultEmpLanguage->getLanguage()->getName());
        $resultEmpLanguage = $empLanguages[1];
        $this->assertEquals('Kayla', $resultEmpLanguage->getEmployee()->getFirstName());
        $this->assertEquals('Spanish', $resultEmpLanguage->getLanguage()->getName());
    }

    public function testDeleteEmployeeLanguages(): void
    {
        $entriesToDelete = [['languageId' => 2, 'fluencyId' => 100]];
        $empLanguagesCount = $this->employeeLanguageDao->deleteEmployeeLanguages(2, $entriesToDelete);
        $this->assertEquals(0, $empLanguagesCount);

        $entriesToDelete = [
            ['languageId' => 1, 'fluencyId' => 2],
            ['languageId' => 2, 'fluencyId' => 1]
        ];
        $empLanguagesCount = $this->employeeLanguageDao->deleteEmployeeLanguages(1, $entriesToDelete);
        $this->assertEquals(2, $empLanguagesCount);
    }

    public function testGetAllowedEmployeeLanguages(): void
    {
        $searchFilterParams = new EmployeeAllowedLanguageSearchFilterParams();
        $searchFilterParams->setEmpNumber(1);
        $languages = $this->employeeLanguageDao->getAllowedEmployeeLanguages($searchFilterParams);

        $this->assertCount(3, $languages);
        $this->assertEquals(
            ['Dutch', 'English', 'Spanish'],
            array_map(
                function (Language $language) {
                    return $language->getName();
                },
                $languages
            )
        );

        $searchFilterParams = new EmployeeAllowedLanguageSearchFilterParams();
        $searchFilterParams->setEmpNumber(4);
        $languages = $this->employeeLanguageDao->getAllowedEmployeeLanguages($searchFilterParams);
        $this->assertCount(2, $languages);
        $this->assertEquals(
            ['Dutch', 'Spanish'],
            array_map(
                function (Language $language) {
                    return $language->getName();
                },
                $languages
            )
        );

        $searchFilterParams = new EmployeeAllowedLanguageSearchFilterParams();
        $searchFilterParams->setEmpNumber(100);
        $languages = $this->employeeLanguageDao->getAllowedEmployeeLanguages($searchFilterParams);
        $this->assertCount(3, $languages);
        $this->assertEquals(
            ['Dutch', 'English', 'Spanish'],
            array_map(
                function (Language $language) {
                    return $language->getName();
                },
                $languages
            )
        );
    }

    public function testGetEmployeeAllowedLicensesCount(): void
    {
        $searchFilterParams = new EmployeeAllowedLanguageSearchFilterParams();
        $searchFilterParams->setEmpNumber(1);
        $languagesCount = $this->employeeLanguageDao->getAllowedEmployeeLanguagesCount($searchFilterParams);
        $this->assertEquals(3, $languagesCount);

        $searchFilterParams = new EmployeeAllowedLanguageSearchFilterParams();
        $searchFilterParams->setEmpNumber(4);
        $languagesCount = $this->employeeLanguageDao->getAllowedEmployeeLanguagesCount($searchFilterParams);
        $this->assertEquals(2, $languagesCount);

        $searchFilterParams = new EmployeeAllowedLanguageSearchFilterParams();
        $searchFilterParams->setEmpNumber(100);
        $languagesCount = $this->employeeLanguageDao->getAllowedEmployeeLanguagesCount($searchFilterParams);
        $this->assertEquals(3, $languagesCount);
    }
}
