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

namespace OrangeHRM\Core\Api\V2\Serializer;

use OrangeHRM\Core\Traits\ClassHelperTrait;
use ReflectionClass;
use Traversable;

trait NormalizerTrait
{
    use ClassHelperTrait;

    /**
     * @var string
     */
    protected string $modelClass;

    /**
     * @var array|object
     */
    protected $data;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * @param string $modelClass
     */
    protected function setModelClass(string $modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return array|object
     */
    protected function getData()
    {
        return $this->data;
    }

    /**
     * @param array|object $data
     */
    protected function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     * @throws NormalizeException
     */
    protected function normalizeObject(): array
    {
        $this->checkForValidModelClass();
        $model = $this->getInitializedModelInstance($this->data);
        return $model->toArray();
    }

    /**
     * @return array
     * @throws NormalizeException
     */
    protected function normalizeObjectsArray(): array
    {
        $this->checkForValidModelClass();
        if (!is_iterable($this->data)) {
            throw new NormalizeException(
                sprintf(
                    '$data should be instance of  `%s`',
                    Traversable::class
                )
            );
        }

        $normalizedArray = [];
        foreach ($this->data as $data) {
            $model = $this->getInitializedModelInstance($data);
            $normalizedArray[] = $model->toArray();
        }
        return $normalizedArray;
    }

    /**
     * @throws NormalizeException
     */
    private function checkForValidModelClass(): void
    {
        if (!$this->getClassHelper()->hasClassImplements($this->modelClass, Normalizable::class)) {
            throw new NormalizeException(
                sprintf(
                    'Model class `%s` should implements  `%s`',
                    $this->modelClass,
                    Normalizable::class
                )
            );
        }
    }

    /**
     * @param $data
     * @return Normalizable
     */
    private function getInitializedModelInstance($data): Normalizable
    {
        if ($this->getClassHelper()->hasClassImplements($this->modelClass, ModelConstructorArgsAwareInterface::class)) {
            return (new ReflectionClass($this->modelClass))->newInstanceArgs($data);
        }
        return new $this->modelClass($data);
    }
}
