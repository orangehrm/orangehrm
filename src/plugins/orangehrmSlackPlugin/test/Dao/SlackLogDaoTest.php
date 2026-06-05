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

namespace OrangeHRM\Tests\Slack\Dao;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\SlackLog;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Dao\SlackLogDao;
use OrangeHRM\Slack\Dao\SlackRegistrationDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Two contracts matter most here:
 *
 *   - hasSuccessfulDeliveryForDate() is the idempotency gate the scheduler reads
 *     every tick — a wrong false-negative would resend; a wrong false-positive
 *     would silently skip a day.
 *   - purgeOlderThan() must respect a strict `<` comparison so the row that
 *     defines the cutoff is preserved.
 *
 * The rest (makeLogFor, recordLog) are simple persistence tests but pin the
 * column shape against the migration.
 *
 * @group Slack
 * @group Dao
 */
class SlackLogDaoTest extends TestCase
{
    private SlackLogDao $logDao;
    private SlackRegistrationDao $registrationDao;
    private SlackRegistration $registration;

    protected function setUp(): void
    {
        $this->logDao = new SlackLogDao();
        $this->registrationDao = new SlackRegistrationDao();
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmSlackPlugin/test/fixtures/SlackBaseFixture.yaml';
        TestDataService::populate($fixture);

        // Implicit m2m join table is not auto-truncated by the fixture loader.
        $this->getEntityManager()->getConnection()
            ->executeStatement('DELETE FROM ohrm_workspace_notification_registration_subunit');

        $this->registration = $this->makeRegistration('BIRTHDAY');
        $this->registrationDao->saveRegistration($this->registration);
    }

    /* ─────────────────────── makeLogFor / recordLog ─────────────────────────── */

    public function testMakeLogForCopiesRegistrationFieldsAndStamps(): void
    {
        $date = new DateTime('2026-06-01');

        $log = $this->logDao->makeLogFor(
            $this->registration,
            $date,
            SlackLog::STATUS_SUCCESS,
            7,
            null
        );

        $this->assertSame($this->registration, $log->getRegistration());
        $this->assertSame('BIRTHDAY', $log->getEventType());
        $this->assertSame('2026-06-01', $log->getEventDate()->format('Y-m-d'));
        $this->assertSame(SlackLog::STATUS_SUCCESS, $log->getStatus());
        $this->assertSame(7, $log->getRecipientCount());
        $this->assertNull($log->getErrorMessage());
    }

    public function testRecordLogPersistsRowAndStampsCreatedAt(): void
    {
        $log = $this->logDao->makeLogFor(
            $this->registration,
            new DateTime('2026-06-01'),
            SlackLog::STATUS_SUCCESS,
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
        // The DAO only stamps when null — callers that have their own clock
        // should not be overwritten.
        $preset = new DateTime('2020-01-01 12:00:00');
        $log = $this->logDao->makeLogFor(
            $this->registration,
            new DateTime('2020-01-01'),
            SlackLog::STATUS_FAILED,
            0,
            'manual'
        );
        $log->setCreatedAt($preset);
        $this->logDao->recordLog($log);

        $this->assertEquals($preset, $log->getCreatedAt());
    }

    /* ────────────────────── hasSuccessfulDeliveryForDate ────────────────────── */

    public function testHasSuccessfulDeliveryReturnsTrueOnlyForMatchingRegistrationDateAndStatus(): void
    {
        $date = new DateTime('2026-06-01');
        $this->recordLogFor($this->registration, $date, SlackLog::STATUS_SUCCESS);

        $this->assertTrue(
            $this->logDao->hasSuccessfulDeliveryForDate($this->registration->getId(), $date)
        );
    }

    public function testHasSuccessfulDeliveryDoesNotCountFailedRow(): void
    {
        // A FAILED row for today must NOT satisfy the idempotency check —
        // otherwise the scheduler would skip retrying tomorrow.
        $date = new DateTime('2026-06-01');
        $this->recordLogFor($this->registration, $date, SlackLog::STATUS_FAILED, 0, 'boom');

        $this->assertFalse(
            $this->logDao->hasSuccessfulDeliveryForDate($this->registration->getId(), $date)
        );
    }

    public function testHasSuccessfulDeliveryDoesNotCountSkippedRow(): void
    {
        $date = new DateTime('2026-06-01');
        $this->recordLogFor($this->registration, $date, SlackLog::STATUS_SKIPPED);

        $this->assertFalse(
            $this->logDao->hasSuccessfulDeliveryForDate($this->registration->getId(), $date)
        );
    }

    public function testHasSuccessfulDeliveryIsScopedToRegistrationId(): void
    {
        // Two registrations on the same day: a success on registration A must
        // not satisfy the check for registration B.
        $other = $this->makeRegistration('LEAVE_TODAY');
        $this->registrationDao->saveRegistration($other);

        $date = new DateTime('2026-06-01');
        $this->recordLogFor($this->registration, $date, SlackLog::STATUS_SUCCESS);

        $this->assertFalse(
            $this->logDao->hasSuccessfulDeliveryForDate($other->getId(), $date)
        );
    }

    public function testHasSuccessfulDeliveryIsScopedToDate(): void
    {
        $today = new DateTime('2026-06-01');
        $yesterday = new DateTime('2026-05-31');
        $this->recordLogFor($this->registration, $yesterday, SlackLog::STATUS_SUCCESS);

        $this->assertFalse(
            $this->logDao->hasSuccessfulDeliveryForDate($this->registration->getId(), $today)
        );
    }

    /* ─────────────────────────── purgeOlderThan ──────────────────────────────── */

    public function testPurgeOlderThanDeletesRowsStrictlyBeforeCutoff(): void
    {
        $old = $this->recordLogFor($this->registration, new DateTime('2026-05-01'), SlackLog::STATUS_SUCCESS);
        $this->setCreatedAt($old, new DateTime('2026-05-01 09:00:00'));

        $boundary = $this->recordLogFor($this->registration, new DateTime('2026-05-02'), SlackLog::STATUS_SUCCESS);
        $this->setCreatedAt($boundary, new DateTime('2026-05-02 00:00:00'));

        $recent = $this->recordLogFor($this->registration, new DateTime('2026-05-03'), SlackLog::STATUS_SUCCESS);
        $this->setCreatedAt($recent, new DateTime('2026-05-03 09:00:00'));

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $deleted = $this->logDao->purgeOlderThan(new DateTime('2026-05-02 00:00:00'));

        $this->assertSame(1, $deleted, 'Only the row strictly before the cutoff should go');
        // boundary + recent survive
        $this->assertCount(2, $this->getEntityManager()->getRepository(SlackLog::class)->findAll());
    }

    public function testPurgeOlderThanWithNoMatchingRowsReturnsZero(): void
    {
        $this->recordLogFor($this->registration, new DateTime('2026-06-01'), SlackLog::STATUS_SUCCESS);

        $deleted = $this->logDao->purgeOlderThan(new DateTime('2020-01-01'));

        $this->assertSame(0, $deleted);
    }

    /* ─────────────────────────── Helpers ─────────────────────────────────────── */

    private function makeRegistration(string $eventType): SlackRegistration
    {
        $reg = new SlackRegistration();
        $reg->setProvider(SlackRegistration::PROVIDER_SLACK);
        $reg->setEventType($eventType);
        $reg->setWebhookUrl('encrypted-blob');
        $reg->setTimezone('UTC');
        $reg->setDailySendTime('09:00');
        $reg->setActive(true);
        return $reg;
    }

    private function recordLogFor(
        SlackRegistration $registration,
        DateTime $eventDate,
        string $status,
        int $count = 1,
        ?string $error = null
    ): SlackLog {
        $log = $this->logDao->makeLogFor($registration, $eventDate, $status, $count, $error);
        return $this->logDao->recordLog($log);
    }

    private function setCreatedAt(SlackLog $log, DateTime $when): void
    {
        $log->setCreatedAt($when);
        $this->getEntityManager()->persist($log);
    }
}
