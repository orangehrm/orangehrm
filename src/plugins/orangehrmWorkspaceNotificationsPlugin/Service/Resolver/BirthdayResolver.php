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

namespace OrangeHRM\WorkspaceNotifications\Service\Resolver;

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRecipient;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationSettingsService;

class BirthdayResolver implements RecipientResolverInterface
{
    use EntityManagerHelperTrait;

    private WorkspaceNotificationSettingsService $settings;

    public function __construct(?WorkspaceNotificationSettingsService $settings = null)
    {
        $this->settings = $settings ?? new WorkspaceNotificationSettingsService();
    }

    /**
     * @param int[] $subunitIds Multi-subunit filter; empty array = include all employees.
     * @return WorkspaceNotificationRecipient[]
     */
    public function resolve(DateTime $date, array $subunitIds): array
    {
        $month = (int)$date->format('n');
        $day = (int)$date->format('j');
        $year = (int)$date->format('Y');
        $isLeapYear = checkdate(2, 29, $year);

        $qb = $this->createQueryBuilder(Employee::class, 'e')
            ->leftJoin('e.subDivision', 'sub')
            ->addSelect('sub')
            ->andWhere('e.employeeTerminationRecord IS NULL')
            ->andWhere('e.birthday IS NOT NULL')
            ->orderBy('e.firstName', 'ASC')
            ->addOrderBy('e.lastName', 'ASC');

        if (count($subunitIds) > 0) {
            $qb->andWhere('IDENTITY(e.subDivision) IN (:subunitIds)')
                ->setParameter('subunitIds', $subunitIds);
        }

        // Whether Feb 29 employees should also be included as aliases on this date.
        $includeFeb29Alias = $this->shouldIncludeFeb29Alias($month, $day, $isLeapYear);

        // MONTH()/DAY() aren't registered as DQL functions in OHRM, so we filter in PHP.
        // The dataset is whole-organisation × birthday-not-null, which is small enough.
        $matches = [];
        foreach ($qb->getQuery()->getResult() as $emp) {
            /** @var Employee $emp */
            $birthday = $emp->getBirthday();
            if ($birthday === null) {
                continue;
            }
            $bMonth = (int)$birthday->format('n');
            $bDay = (int)$birthday->format('j');
            $isFeb29Birthday = ($bMonth === 2 && $bDay === 29);

            $normalMatch = ($bMonth === $month && $bDay === $day);
            $aliasMatch = ($isFeb29Birthday && $includeFeb29Alias);

            if ($normalMatch || $aliasMatch) {
                $matches[] = new WorkspaceNotificationRecipient(
                    trim($emp->getFirstName() . ' ' . $emp->getLastName()),
                    $emp->getSubDivision() ? $emp->getSubDivision()->getName() : null
                );
            }
        }
        return $matches;
    }

    /**
     * Returns true when Feb 29 employees should be included as aliases on this date,
     * based on the configured leap year mode and whether this is a non-leap year.
     */
    private function shouldIncludeFeb29Alias(int $month, int $day, bool $isLeapYear): bool
    {
        if ($isLeapYear) {
            return false;
        }

        $mode = $this->settings->getBirthdayLeapYearMode();

        return match ($mode) {
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_FEB_28 => ($month === 2 && $day === 28),
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_MARCH_1 => ($month === 3 && $day === 1),
            default => false,
        };
    }
}
