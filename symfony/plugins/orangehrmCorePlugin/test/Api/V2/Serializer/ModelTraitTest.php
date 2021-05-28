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

namespace OrangeHRM\Tests\Core\Api\V2\Serializer;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Core
 * @group Serializer
 * @group Model
 */
class ModelTraitTest extends TestCase
{
    public function testSetEntity(): void
    {
        $testEntity = new TestJobTitleEntity();
        $testEntity->setName('TestName');
        $testEntity->setDescription('TestValue');

        $testModel = new TestJobTitleModel($testEntity);
        $array = $testModel->toArray();
        $this->assertEquals(['name' => 'TestName'], $array);
        $this->assertEquals(1, count($array));
    }

    public function testSetFilters(): void
    {
        $testEntity = new TestJobTitleEntity();
        $testEntity->setName('TestName');
        $testEntity->setDescription('TestValue');

        $testModel = new TestJobTitleModel($testEntity);
        $testModel->setTestFilters(['description']);
        $array = $testModel->toArray();
        $this->assertEquals(['description' => 'TestValue'], $array);
        $this->assertEquals(1, count($array));
    }

    public function testSetAttributeNames(): void
    {
        $testEntity = new TestJobTitleEntity();
        $testEntity->setName('TestUniqueName');
        $testEntity->setDescription('TestValue');

        $testModel = new TestJobTitleModel($testEntity);
        $testModel->setTestFilters(['name', 'description']);
        $testModel->setTestAttributeNames(['uniqueName', 'value']);
        $array = $testModel->toArray();
        $this->assertEquals(['value' => 'TestValue', 'uniqueName' => 'TestUniqueName'], $array);
        $this->assertEquals(2, count($array));
    }

    public function testToArrayWithChaining(): void
    {
        $testEmpEntity = new TestEmployeeEntity();
        $testEmpEntity->setId(1);
        $testEmpEntity->setJobTitle(null);

        $testModel = new TestEmployeeModel($testEmpEntity);
        $this->assertEquals(
            ['id' => 1, 'getJobTitle' => ['getName' => null, 'getDescription' => null]],
            $testModel->toArray()
        );

        $testJobTitleEntity = new TestJobTitleEntity();
        $testJobTitleEntity->setName('Name');
        $testJobTitleEntity->setDescription('Description');
        $testEmpEntity->setJobTitle($testJobTitleEntity);
        $testModel = new TestEmployeeModel($testEmpEntity);
        $this->assertEquals(
            ['id' => 1, 'getJobTitle' => ['getName' => 'Name', 'getDescription' => 'Description']],
            $testModel->toArray()
        );

        $testModel = new TestEmployeeModel($testEmpEntity);
        $testModel->setTestAttributeNames(
            [
                'empId',
                ['jobTitle', 'name'],
                ['jobTitle', 'description'],
            ]
        );
        $this->assertEquals(
            ['empId' => 1, 'jobTitle' => ['name' => 'Name', 'description' => 'Description']],
            $testModel->toArray()
        );

        $testModel = new TestEmployeeModel($testEmpEntity);
        $testModel->setTestFilters(
            [
                'id',
                ['getJobTitle', 'getName'],
            ]
        );
        $testModel->setTestAttributeNames(
            [
                'empId',
                ['jobTitle', 'name'],
            ]
        );
        $this->assertEquals(
            ['empId' => 1, 'jobTitle' => ['name' => 'Name']],
            $testModel->toArray()
        );
    }

    public function testMakeNestedArray(): void
    {
        $result = $this->invokePrivateMethod(
            TestModel::class,
            'makeNestedArray',
            [['model', 'id'], "Value"]
        );
        $this->assertEquals(['model' => ['id' => "Value"]], $result);

        $result = $this->invokePrivateMethod(
            TestModel::class,
            'makeNestedArray',
            [['model', 'id'], null]
        );
        $this->assertEquals(['model' => ['id' => null]], $result);

        $result = $this->invokePrivateMethod(
            TestModel::class,
            'makeNestedArray',
            [['model', 'obj'], ['id' => 1, 'name' => 'value']]
        );
        $this->assertEquals(['model' => ['obj' => ['id' => 1, 'name' => 'value']]], $result);
    }
}

class TestJobTitleEntity
{
    private $name = null;
    private $description = null;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}

class TestEmployeeEntity
{
    private ?int $id = null;
    private ?TestJobTitleEntity $jobTitle = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return TestJobTitleEntity|null
     */
    public function getJobTitle(): ?TestJobTitleEntity
    {
        return $this->jobTitle;
    }

    /**
     * @param TestJobTitleEntity|null $jobTitle
     */
    public function setJobTitle(?TestJobTitleEntity $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }
}

class TestJobTitleModel implements Normalizable
{
    use ModelTrait;

    public function __construct(TestJobTitleEntity $testEntity)
    {
        $this->setEntity($testEntity);
        $this->setFilters(['name']);
    }

    public function setTestFilters($filters)
    {
        $this->setFilters($filters);
    }

    public function setTestAttributeNames($attributeNames)
    {
        $this->setAttributeNames($attributeNames);
    }
}

class TestEmployeeModel implements Normalizable
{
    use ModelTrait;

    public function __construct(TestEmployeeEntity $testEntity)
    {
        $this->setEntity($testEntity);
        $this->setFilters(
            [
                'id',
                ['getJobTitle', 'getName'],
                ['getJobTitle', 'getDescription'],
            ]
        );
    }

    public function setTestFilters($filters)
    {
        $this->setFilters($filters);
    }

    public function setTestAttributeNames($attributeNames)
    {
        $this->setAttributeNames($attributeNames);
    }
}

class TestModel implements Normalizable
{
    use ModelTrait;
}
