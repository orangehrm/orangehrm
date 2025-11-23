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

namespace OrangeHRM\Tests\Admin\Dao;

use OrangeHRM\Admin\Dao\CompanyStructureDao;
use OrangeHRM\Admin\Dao\OrganizationDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Organization;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Exception;

/**
 * @group Admin
 * @group Dao
 */
class OrganizationDaoTest extends TestCase
{
    private OrganizationDao $organizationDao;
    private CompanyStructureDao $companyStructureDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->organizationDao = new OrganizationDao();
        $this->companyStructureDao = new CompanyStructureDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/OrganizationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetOrganizationGeneralInformation(): void
    {
        $this->assertTrue($this->organizationDao->getOrganizationGeneralInformation() instanceof Organization);
    }

    public function testSaveOrganizationGeneralInformation(): void
    {
        $organization = new Organization();
        $organization->setName("OrangeHRM");
        $organization->setTaxId('1234');
        $organization->setRegistrationNumber('1234');
        $organization->setPhone('01123456789');
        $organization->setFax('01123456789');
        $organization->setEmail('test@orangehrm.com');
        $organization->setCountry('SL');
        $organization->setProvince('Western');
        $organization->setCity('Panadura');
        $organization->setZipCode('12500');
        $organization->setStreet1('street1');
        $organization->setStreet2('street2');
        $organization->setNote('test note');
        $result = $this->organizationDao->saveOrganizationGeneralInformation($organization);
        $this->assertTrue($result instanceof Organization);
        $this->assertEquals("OrangeHRM", $result->getName());
        $this->assertEquals("1234", $result->getTaxId());

        $updatedSubunit = $this->companyStructureDao->getSubunitById(1);
        $this->assertEquals('OrangeHRM', $updatedSubunit->getName());
    }
}
