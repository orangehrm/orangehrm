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
class ModelTraitNormalizeNestedCollectionTest extends TestCase
{
    public function testCallNestedCollections(): void
    {
        $emp1 = new TestEmployee();
        $emp1->setId(1);

        $emp2 = new TestEmployee();
        $emp2->setId(2);

        $emp3 = new TestEmployee();
        $emp3->setId(3);

        $emp1->setSupervisors([$emp2, $emp3]);

        $result = $this->invokePrivateMethod(
            EmployeeModel::class,
            'normalizeNestedCollection',
            [[$emp1], ['getId'], ['id']]
        );

        $this->assertEquals([['id' => 1]], $result);
    }

    public function testCallNestedCollectionsFor(): void
    {
        $emp1 = new TestEmployee();
        $emp1->setId(1);

        $emp2 = new TestEmployee();
        $emp2->setId(2);

        $emp3 = new TestEmployee();
        $emp3->setId(3);

        $emp4 = new TestEmployee();
        $emp4->setId(4);

        $emp5 = new TestEmployee();
        $emp5->setId(5);

        $emp1->setSupervisors([$emp2, $emp3]);
        $emp4->setSupervisors([$emp1, $emp5]);
        $emp4->setName('Test');

        $result = $this->invokePrivateMethod(
            EmployeeModel::class,
            'normalizeNestedCollection',
            [[$emp1, $emp4], ['getId', 'getName'], ['id', 'name']]
        );

        $this->assertEquals([['id' => 1, 'name' => null], ['id' => 4, 'name' => 'Test']], $result);
    }
}

class TestEmployee
{
    private ?int $id = null;
    private ?string $name = null;
    private array $supervisors = [];

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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getSupervisors(): array
    {
        return $this->supervisors;
    }

    /**
     * @param array $supervisors
     */
    public function setSupervisors(array $supervisors): void
    {
        $this->supervisors = $supervisors;
    }
}

class EmployeeModel implements Normalizable
{
    use ModelTrait;
}
