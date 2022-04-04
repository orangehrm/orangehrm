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

namespace OrangeHRM\Core\Api\V2\Validator\Rules;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;

class EntityUniqueProperty extends AbstractRule
{
    use EntityManagerHelperTrait;

    /**
     * @var string
     */
    private string $entityName;

    /**
     * @var string
     */
    private string $property;

    /**
     * @var EntityUniquePropertyOption
     */
    private EntityUniquePropertyOption $option;

    public function __construct(string $entityName, string $property, ?EntityUniquePropertyOption $option = null)
    {
        $this->entityName = $entityName;
        $this->property = $property;
        $this->option = $option ?? new EntityUniquePropertyOption();
    }


    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if ($this->option->isTrim()) {
            $input = $this->option->getTrimFunction()(($input));
        }

        $entityList = $this->getRepository($this->entityName)->findBy([$this->property => $input]);

        if (empty($entityList)) {
            return true;
        }

        return $this->option->hasIgnoreValues() && $this->entitiesHaveIgnoreValues($entityList);
    }

    /**
     * @param array $entities
     * @return bool
     */
    private function entitiesHaveIgnoreValues(array $entities): bool
    {
        $lastGetter = array_key_last($this->option->getIgnoreValues());
        foreach ($entities as $entity) {
            foreach ($this->option->getIgnoreValues() as $getter => $value) {
                if ($entity->$getter() === $value) {
                    break; //if entity has ignored value, skip to next entity
                }
                if ($getter === $lastGetter) {
                    return false; //if this point reached, entity has no ignored values
                }
            }
        }
        return true; //all entities have ignored values
    }
}
