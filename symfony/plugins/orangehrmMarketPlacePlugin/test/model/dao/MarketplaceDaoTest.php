<?php
/*
 *
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
 *
 */

/**
 * @group Marketplace
 */
class MarketplaceDaoTest extends PHPUnit_Framework_TestCase {

    private $marketplaceDao;
    private $fixture;

    protected function setUp() {
        $this->marketplaceDao = new MarketplaceDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmMarketPlacePlugin/test/fixtures/MarketplaceDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testChangeAddonStatus() {
        $paidAddonNames = array("LDAP");
        $result = $this->marketplaceDao->changeAddonStatus(
            $paidAddonNames,
            MarketplaceDao::ADDON_STATUS_REQUESTED,
            MarketplaceDao::ADDON_STATUS_PAID
        );
        $this->assertEquals($result, true);

    }

    public function testGetAddonByStatus() {
        $result = $this->marketplaceDao->getAddonByStatus(MarketplaceDao::ADDON_STATUS_PAID);
        $this->assertEquals(2, count($result));
    }

    public function testUpdateAddon() {
        $data = array(
            'id' => 4,
            'status' => 'Installed',
            'pluginName' => 'orangehrmClaimPlugin'
        );

        $result = $this->marketplaceDao->updateAddon($data);
        $this->assertEquals(true, $result);
    }

    public function testGetPaidTypeInstalledAddons() {
        $result = $this->marketplaceDao->getPaidTypeInstalledAddons();
        $this->assertEquals(1, count($result));
        $this->assertEquals(5, $result[0]['id']);
    }

    public function testGetAddonById() {
        $result = $this->marketplaceDao->getAddonById(1);
        $this->assertInstanceOf('Addon', $result);
        $this->assertEquals("orangehrmLDAPPlugin", $result->getPluginName());
    }

}
