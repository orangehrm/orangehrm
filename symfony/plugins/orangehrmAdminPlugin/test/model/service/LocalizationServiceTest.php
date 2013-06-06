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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Admin
 */
class LocalizationServiceTest extends PHPUnit_Framework_TestCase {

    private $localizationService;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->localizationService = new LocalizationService();
    }

    public function testConvertPHPFormatDateToISOFormatDate() {

       $this->assertEquals("1988-03-11", $this->localizationService->convertPHPFormatDateToISOFormatDate('d-m-Y', "11-03-1988"));
       $this->assertEquals("Invalid date", $this->localizationService->convertPHPFormatDateToISOFormatDate('d-m-Y', "1988-03-11"));

       $this->assertEquals("1988-03-11", $this->localizationService->convertPHPFormatDateToISOFormatDate('m-d-Y', "03-11-1988"));
       $this->assertEquals("Invalid date", $this->localizationService->convertPHPFormatDateToISOFormatDate('m-d-Y', "1988-00-11"));

       $this->assertEquals("1988-03-11", $this->localizationService->convertPHPFormatDateToISOFormatDate('Y-d-m', "1988-11-03"));
       $this->assertEquals("Invalid date", $this->localizationService->convertPHPFormatDateToISOFormatDate('Y-d-m', "1988-1223"));
    }

}
