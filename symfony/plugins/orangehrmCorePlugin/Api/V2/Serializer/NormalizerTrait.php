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

namespace OrangeHRM\Core\Api\V2\Serializer;

use Traversable;

trait NormalizerTrait
{
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
        $model = new $this->modelClass($this->data);
        if ($model instanceof Normalizable) {
            return $model->toArray();
        }
        throw new NormalizeException(
            sprintf(
                'Model class should be instance of  `%s`',
                Normalizable::class
            )
        );
    }

    /**
     * @return array
     * @throws NormalizeException
     */
    protected function normalizeObjectsArray(): array
    {
        if (is_iterable($this->data)) {
            $normalizedArray = [];
            foreach ($this->data as $data) {
                $model = new $this->modelClass($data);
                if ($model instanceof Normalizable) {
                    $normalizedArray[] = $model->toArray();
                } else {
                    throw new NormalizeException(
                        sprintf(
                            'Model class should be instance of  `%s`',
                            Normalizable::class
                        )
                    );
                }
            }
            return $normalizedArray;
        }

        throw new NormalizeException(
            sprintf(
                '$data should be instance of  `%s`',
                Traversable::class
            )
        );
    }
}
