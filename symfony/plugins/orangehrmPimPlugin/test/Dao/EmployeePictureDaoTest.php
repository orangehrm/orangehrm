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
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Pim\Dao\EmployeePictureDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeePictureDaoTest extends TestCase
{
    use EntityManagerHelperTrait;

    private EmployeePictureDao $employeePictureDao;

    protected function setUp(): void
    {
        $this->employeePictureDao = new EmployeePictureDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeePictureDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveEmployeePicture(): void
    {
        $filename = 'profile.jpeg';
        $fileType = 'image/jpeg';
        $size = '20692';
        $picture = "Test bloab";
        $width = '200';
        $height = '200';

        $employee = $this->getReference(Employee::class, 2);
        $empPicture = new EmpPicture();
        $empPicture->setFilename($filename);
        $empPicture->setFileType($fileType);
        $empPicture->setSize($size);
        $empPicture->setPicture($picture);
        $empPicture->setWidth($width);
        $empPicture->setHeight($height);
        $empPicture->setEmployee($employee);

        $empPicture = $this->employeePictureDao->saveEmployeePicture($empPicture);
        $this->assertEquals($filename, $empPicture->getFilename());
        $this->assertEquals($fileType, $empPicture->getFileType());
        $this->assertEquals($size, $empPicture->getSize());
        $this->assertEquals($picture, $empPicture->getPicture());
        $this->assertEquals($picture, $empPicture->getDecorator()->getPicture());
        $this->assertEquals($width, $empPicture->getWidth());
        $this->assertEquals($height, $empPicture->getHeight());
        $this->assertEquals('Ashley', $empPicture->getEmployee()->getFirstName());

        $empPicture->setFilename('Updated filename');
        $empPicture = $this->employeePictureDao->saveEmployeePicture($empPicture);
        $this->assertEquals('Updated filename', $empPicture->getFilename());
    }

    public function testGetEmpPictureByEmpNumber(): void
    {
        $empPicture = $this->employeePictureDao->getEmpPictureByEmpNumber(1);
        $this->assertTrue($empPicture instanceof EmpPicture);
        $this->assertEquals('profile.jpeg', $empPicture->getFilename());
        $this->assertEquals('image/jpeg', $empPicture->getFileType());

        $empPicture = $this->employeePictureDao->getEmpPictureByEmpNumber(3);
        $this->assertNull($empPicture);

        $empPicture = $this->employeePictureDao->getEmpPictureByEmpNumber(100);
        $this->assertNull($empPicture);
    }
}
