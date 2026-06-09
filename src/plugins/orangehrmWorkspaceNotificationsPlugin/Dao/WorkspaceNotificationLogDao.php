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

namespace OrangeHRM\WorkspaceNotifications\Dao;

use DateTime;
use DateTimeZone;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\WorkspaceNotificationLog;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;

class WorkspaceNotificationLogDao extends BaseDao
{
    public function hasSuccessfulDeliveryForDate(int $registrationId, DateTime $date): bool
    {
        return $this->hasLogForDateWithStatus(
            $registrationId,
            $date,
            WorkspaceNotificationLog::STATUS_SUCCESS
        );
    }

    /**
     * True if a log row already exists for this registration on this date
     * with the given status. Used by the dispatcher to avoid writing duplicate
     * SKIPPED rows on every 5-minute cron tick when there are no recipients —
     * the first SKIPPED of the day stands; subsequent ticks re-evaluate but
     * write nothing new.
     */
    public function hasLogForDateWithStatus(int $registrationId, DateTime $date, string $status): bool
    {
        $q = $this->createQueryBuilder(WorkspaceNotificationLog::class, 'l')
            ->select('COUNT(l.id)')
            ->andWhere('IDENTITY(l.registration) = :registrationId')
            ->setParameter('registrationId', $registrationId)
            ->andWhere('l.eventDate = :eventDate')
            ->setParameter('eventDate', $date->format('Y-m-d'))
            ->andWhere('l.status = :status')
            ->setParameter('status', $status);

        return ((int)$q->getQuery()->getSingleScalarResult()) > 0;
    }

    public function recordLog(WorkspaceNotificationLog $log): WorkspaceNotificationLog
    {
        if ($log->getCreatedAt() === null) {
            $log->setCreatedAt(new DateTime('now', new DateTimeZone('UTC')));
        }
        $this->persist($log);
        return $log;
    }

    public function purgeOlderThan(DateTime $cutoff): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->delete(WorkspaceNotificationLog::class, 'l')
            ->andWhere('l.createdAt < :cutoff')
            ->setParameter('cutoff', $cutoff);
        return (int)$qb->getQuery()->execute();
    }

    public function makeLogFor(
        WorkspaceNotificationRegistration $registration,
        DateTime $eventDate,
        string $status,
        int $recipientCount = 0,
        ?string $errorMessage = null
    ): WorkspaceNotificationLog {
        $log = new WorkspaceNotificationLog();
        $log->setRegistration($registration);
        $log->setEventType($registration->getEventType());
        $log->setEventDate($eventDate);
        $log->setStatus($status);
        $log->setRecipientCount($recipientCount);
        $log->setErrorMessage($errorMessage);
        return $log;
    }
}
