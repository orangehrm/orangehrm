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

namespace OrangeHRM\Tests\Util;

use OrangeHRM\Core\Traits\ORM\EntityManagerTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

abstract class TestCase extends PHPUnitTestCase
{
    use EntityManagerTrait;

    /**
     * @param string $entityName
     * @param mixed $id
     * @return object|null The entity reference.
     *
     * @template T
     * @psalm-param class-string<T> $entityName
     * @psalm-return ?T
     */
    protected function getEntityReference(string $entityName, $id)
    {
        return $this->getEntityManager()->getReference($entityName, $id);
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    protected static function getClassMethod(string $className, string $methodName): ReflectionMethod
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param array $methodParams
     * @param array $constructorParams
     * @return mixed
     */
    protected function invokePrivateMethod(
        string $className,
        string $methodName,
        array $methodParams = [],
        array $constructorParams = []
    ) {
        $method = self::getClassMethod($className, $methodName);
        $instance = (new ReflectionClass($className))->newInstanceArgs($constructorParams);
        return $method->invokeArgs($instance, $methodParams);
    }

    /**
     * @param string $className
     * @param MockObject $mockInstance
     * @param string $methodName
     * @param array $methodParams
     * @return mixed
     */
    protected function invokePrivateMethodOnMock(
        string $className,
        MockObject $mockInstance,
        string $methodName,
        array $methodParams = []
    ) {
        $method = self::getClassMethod($className, $methodName);
        return $method->invokeArgs($mockInstance, $methodParams);
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param array $methodParams
     * @param array $constructorParams
     * @return mixed
     */
    protected function invokeProtectedMethod(
        string $className,
        string $methodName,
        array $methodParams = [],
        array $constructorParams = []
    ) {
        return $this->invokePrivateMethod($className, $methodName, $methodParams, $constructorParams);
    }

    /**
     * @param string $className
     * @param MockObject $mockInstance
     * @param string $methodName
     * @param array $methodParams
     * @return mixed
     */
    protected function invokeProtectedMethodOnMock(
        string $className,
        MockObject $mockInstance,
        string $methodName,
        array $methodParams = []
    ) {
        return $this->invokePrivateMethodOnMock($className, $mockInstance, $methodName, $methodParams);
    }
}
