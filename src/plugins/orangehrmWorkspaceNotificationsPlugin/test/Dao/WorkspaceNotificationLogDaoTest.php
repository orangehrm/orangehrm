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

namespace OrangeHRM\Tests\WorkspaceNotifications\Dao;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\WorkspaceNotificationLog;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\WorkspaceNotifications\Dao\WorkspaceNotificationLogDao;
use OrangeHRM\WorkspaceNotifications\Dao\WorkspaceNotificationRegistrationDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Slack
 * @group Dao
 */
class WorkspaceNotificationLogDaoTest extends TestCase
{
    private WorkspaceNotificationLogDao $logDao;
    private WorkspaceNotificationRegistrationDao $registrationDao;
    private WorkspaceNotificationRegistration $registration;

    protected function setUp(): void
    {
        $this->logDao = new WorkspaceNotificationLogDao();
        $this->registrationDao = new WorkspaceNotificationRegistrationDao();
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmWorkspaceNotificationsPlugin/test/fixtures/WorkspaceNotificationBaseFixture.yaml';
        TestDataService::populate($fixture);

        // Implicit m2m join table is not auto-truncated by the fixture loader.
        $this->getEntityManager()->getConnection()
            ->executeStatement('DELETE FROM ohrm_workspace_notification_registration_subunit');

        $this->registration = $this->makeRegistration('BIRTHDAY');
        $this->registrationDao->saveRegistration($this->registration);
    }

    public function testMakeLogForCopiesRegistrationFieldsAndStamps(): void
    {
        $date = new DateTime('2026-06-01');

        $log = $this->logDao->makeLogFor(
            $this->registration,
            $date,
            WorkspaceNotificationLog::STATUS_SUCCESS,
            7,
            null
        );

        $this->assertSame($this->registration, $log->getRegistration());
        $this->assertSame('BIRTHDAY', $log->getEventType());
        $this->assertSame('2026-06-01', $log->getEventDate()->format('Y-m-d'));
        $this->assertSame(WorkspaceNotificationLog::STATUS_SUCCESS, $log->getStatus());
        $this->assertSame(7, $log->getRecipientCount());
        $this->assertNull($log->getErrorMessage());
    }

    public function testRecordLogPersistsRowAndStampsCreatedAt(): void
    {
        $log = $this->logDao->makeLogFor(
            $this->registration,
            new DateTime('2026-06-01'),
            WorkspaceNotificationLog::STATUS_SUCCESS,
            3,
            null
        );
        $this->assertNull($log->getCreatedAt(), 'precondition: created_at unset before recordLog');

        $this->logDao->recordLog($log);

        $this->assertNotNull($log->getId());
        $this->assertNotNull($log->getCreatedAt());
    }

    public function testRecordLogPreservesExistingCreatedAt(): void
    {
        $preset = new DateTime('2020-01-01 12:00:00');
        $log = $this->logDao->makeLogFor(
            $this->registration,
            new DateTime('2020-01-01'),
            WorkspaceNotificationLog::STATUS_FAILED,
            0,
            'manual'
        );
        $log->setCreatedAt($preset);
        $this->logDao->recordLog($log);

        $this->assertEquals($preset, $log->getCreatedAt());
    }

    public function testHasSuccessfulDeliveryReturnsTrueOnlyForMatchingRegistrationDateAndStatus(): void
    {
        $date = new DateTime('2026-06-01');
        $this->recordLogFor($this->registration, $date, WorkspaceNotificationLog::STATUS_SUCCESS);

        $this->assertTrue(
            $this->logDao->hasSuccessfulDeliveryForDate($this->registration->getId(), $date)
        );
    }

    public function testHasSuccessfulDeliveryDoesNotCountFailedRow(): void
    {
        $date = new DateTime('2026-06-01');
        $this->recordLogFor($this->registration, $date, WorkspaceNotificationLog::STATUS_FAILED, 0, 'boom');

        $this->assertFalse(
            $this->logDao->hasSuccessfulDeliveryForDate($this->registration->getId(), $date)
        );
    }

    public function testHasSuccessfulDeliveryDoesNotCountSkippedRow(): void
    {
        $date = new DateTime('2026-06-01');
        $this->recordLogFor($this->registration, $date, WorkspaceNotificationLog::STATUS_SKIPPED);

        $this->assertFalse(
            $this->logDao->hasSuccessfulDeliveryForDate($this->registration->getId(), $date)
        );
    }

    public function testHasSuccessfulDeliveryIsScopedToRegistrationId(): void
    {
        $other = $this->makeRegistration('LEAVE_TODAY');
        $this->registrationDao->saveRegistration($other);

        $date = new DateTime('2026-06-01');
        $this->recordLogFor($this->registration, $date, WorkspaceNotificationLog::STATUS_SUCCESS);

        $this->assertFalse(
            $this->logDao->hasSuccessfulDeliveryForDate($other->getId(), $date)
        );
    }

    public function testHasSuccessfulDeliveryIsScopedToDate(): void
    {
        $today = new DateTime('2026-06-01');
        $yesterday = new DateTime('2026-05-31');
        $this->recordLogFor($this->registration, $yesterday, WorkspaceNotificationLog::STATUS_SUCCESS);

        $this->assertFalse(
            $this->logDao->hasSuccessfulDeliveryForDate($this->registration->getId(), $today)
        );
    }

    public function testPurgeOlderThanDeletesRowsStrictlyBeforeCutoff(): void
    {
        $old = $this->recordLogFor($this->registration, new DateTime('2026-05-01'), WorkspaceNotificationLog::STATUS_SUCCESS);
        $this->setCreatedAt($old, new DateTime('2026-05-01 09:00:00'));

        $boundary = $this->recordLogFor($this->registration, new DateTime('2026-05-02'), WorkspaceNotificationLog::STATUS_SUCCESS);
        $this->setCreatedAt($boundary, new DateTime('2026-05-02 00:00:00'));

        $recent = $this->recordLogFor($this->registration, new DateTime('2026-05-03'), WorkspaceNotificationLog::STATUS_SUCCESS);
        $this->setCreatedAt($recent, new DateTime('2026-05-03 09:00:00'));

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $deleted = $this->logDao->purgeOlderThan(new DateTime('2026-05-02 00:00:00'));

        $this->assertSame(1, $deleted, 'Only the row strictly before the cutoff should go');
        $this->assertCount(2, $this->getEntityManager()->getRepository(WorkspaceNotificationLog::class)->findAll());
    }

    public function testPurgeOlderThanWithNoMatchingRowsReturnsZero(): void
    {
        $this->recordLogFor($this->registration, new DateTime('2026-06-01'), WorkspaceNotificationLog::STATUS_SUCCESS);

        $deleted = $this->logDao->purgeOlderThan(new DateTime('2020-01-01'));

        $this->assertSame(0, $deleted);
    }

    private function makeRegistration(string $eventType): WorkspaceNotificationRegistration
    {
        $reg = new WorkspaceNotificationRegistration();
        $reg->setProvider(WorkspaceNotificationRegistration::PROVIDER_SLACK);
        $reg->setEventType($eventType);
        $reg->setWebhookUrl('encrypted-blob');
        $reg->setTimezone('UTC');
        $reg->setDailySendTime('09:00');
        $reg->setActive(true);
        return $reg;
    }

    private function recordLogFor(
        WorkspaceNotificationRegistration $registration,
        DateTime $eventDate,
        string $status,
        int $count = 1,
        ?string $error = null
    ): WorkspaceNotificationLog {
        $log = $this->logDao->makeLogFor($registration, $eventDate, $status, $count, $error);
        return $this->logDao->recordLog($log);
    }

    private function setCreatedAt(WorkspaceNotificationLog $log, DateTime $when): void
    {
        $log->setCreatedAt($when);
        $this->getEntityManager()->persist($log);
    }
}
