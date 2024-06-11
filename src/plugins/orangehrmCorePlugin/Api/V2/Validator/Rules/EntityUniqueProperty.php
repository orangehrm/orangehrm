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

        $qb = $this->createQueryBuilder($this->entityName, 'entity');

        $qb->andWhere($qb->expr()->eq('entity.' . $this->property, ':input'))
            ->setParameter('input', $input);

        // Match all entities that have these values
        if ($this->option->hasMatchValues()) {
            foreach ($this->option->getMatchValues() as $property => $value) {
                if (is_null($value)) {
                    $qb->andWhere($qb->expr()->isNull('entity.' . $property));
                } else {
                    $qb->andWhere($qb->expr()->eq('entity.' . $property, ':' . $property . '_value'))
                        ->setParameter($property . '_value', $value);
                }
            }
        }

        // Match all entities that DON'T have these values
        if ($this->option->hasIgnoreValues()) {
            foreach ($this->option->getIgnoreValues() as $property => $value) {
                $qb->andWhere($qb->expr()->neq('entity.' . $property, ':' . $property . '_value'))
                    ->setParameter($property . '_value', $value);
            }
        }

        $entityList = $qb->getQuery()->execute();

        if (empty($entityList)) {
            return true;
        }

        return false;
    }
}
