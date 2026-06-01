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

namespace OrangeHRM\Slack\Service\Resolver;

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Slack\Dto\SlackEmployeeRecipient;

class BirthdayResolver implements RecipientResolverInterface
{
    use EntityManagerHelperTrait;

    /**
     * @return SlackEmployeeRecipient[]
     */
    public function resolve(DateTime $date, ?int $subunitId): array
    {
        $month = (int)$date->format('n');
        $day = (int)$date->format('j');

        $qb = $this->createQueryBuilder(Employee::class, 'e')
            ->leftJoin('e.subDivision', 'sub')
            ->addSelect('sub')
            ->andWhere('e.employeeTerminationRecord IS NULL')
            ->andWhere('e.birthday IS NOT NULL')
            ->orderBy('e.firstName', 'ASC')
            ->addOrderBy('e.lastName', 'ASC');

        if ($subunitId !== null) {
            $qb->andWhere('IDENTITY(e.subDivision) = :subunitId')
                ->setParameter('subunitId', $subunitId);
        }

        // MONTH()/DAY() aren't registered as DQL functions in OHRM, so we filter in PHP.
        // The dataset is whole-organisation × birthday-not-null, which is small enough.
        $matches = [];
        foreach ($qb->getQuery()->getResult() as $emp) {
            /** @var Employee $emp */
            $birthday = $emp->getBirthday();
            if ($birthday === null) {
                continue;
            }
            if ((int)$birthday->format('n') === $month && (int)$birthday->format('j') === $day) {
                $matches[] = new SlackEmployeeRecipient(
                    trim($emp->getFirstName() . ' ' . $emp->getLastName()),
                    $emp->getSubDivision() ? $emp->getSubDivision()->getName() : null
                );
            }
        }
        return $matches;
    }
}
