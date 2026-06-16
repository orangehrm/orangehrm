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

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\WorkspaceNotifications\Dao\WorkspaceNotificationRegistrationDao;
use OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRegistrationSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Slack
 * @group Dao
 */
class WorkspaceNotificationRegistrationDaoTest extends TestCase
{
    private WorkspaceNotificationRegistrationDao $dao;

    protected function setUp(): void
    {
        $this->dao = new WorkspaceNotificationRegistrationDao();
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmWorkspaceNotificationsPlugin/test/fixtures/WorkspaceNotificationBaseFixture.yaml';
        TestDataService::populate($fixture);

        // TestDataService truncates Doctrine entity tables it sees in the YAML
        // but does NOT touch the implicit m2m join table — purge it manually so
        // a prior test's join rows don't collide with this test's fresh ids.
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeStatement('DELETE FROM ohrm_workspace_notification_registration_subunit');
    }

    public function testListRegistrationsReturnsEmptyOnFreshFixture(): void
    {
        $this->assertSame([], $this->dao->listRegistrations(new WorkspaceNotificationRegistrationSearchFilterParams()));
    }

    public function testListRegistrationsReturnsAllRowsOrderedById(): void
    {
        $first = $this->saveBasicRegistration('BIRTHDAY', 'channel-a');
        $second = $this->saveBasicRegistration('LEAVE_TODAY', 'channel-b');
        $third = $this->saveBasicRegistration('BIRTHDAY', 'channel-c', /* active */ false);

        $result = $this->dao->listRegistrations(new WorkspaceNotificationRegistrationSearchFilterParams());

        $this->assertCount(3, $result);
        $this->assertSame($first->getId(), $result[0]->getId());
        $this->assertSame($second->getId(), $result[1]->getId());
        $this->assertSame($third->getId(), $result[2]->getId());
    }

    public function testListActiveRegistrationsExcludesInactive(): void
    {
        $active = $this->saveBasicRegistration('BIRTHDAY', 'active');
        $this->saveBasicRegistration('LEAVE_TODAY', 'inactive', /* active */ false);

        $result = $this->dao->listActiveRegistrations();

        $this->assertCount(1, $result);
        $this->assertSame($active->getId(), $result[0]->getId());
    }

    public function testListActiveRegistrationsEmptyWhenAllInactive(): void
    {
        $this->saveBasicRegistration('BIRTHDAY', 'a', false);
        $this->saveBasicRegistration('LEAVE_TODAY', 'b', false);

        $this->assertSame([], $this->dao->listActiveRegistrations());
    }

    public function testGetRegistrationByIdReturnsEntity(): void
    {
        $saved = $this->saveBasicRegistration('BIRTHDAY', 'lookup');
        $found = $this->dao->getRegistration($saved->getId());

        $this->assertInstanceOf(WorkspaceNotificationRegistration::class, $found);
        $this->assertSame('BIRTHDAY', $found->getEventType());
        $this->assertSame('lookup', $found->getChannelLabel());
    }

    public function testGetRegistrationByUnknownIdReturnsNull(): void
    {
        $this->assertNull($this->dao->getRegistration(999999));
    }

    public function testSaveRegistrationAssignsIdAndRoundTrips(): void
    {
        $reg = $this->makeRegistration('BIRTHDAY', 'fresh');
        $saved = $this->dao->saveRegistration($reg);

        $this->assertSame($reg, $saved, 'DAO must return the same instance after persist');
        $this->assertNotNull($saved->getId(), 'Doctrine should have assigned an id');

        $this->getEntityManager()->clear();
        $reloaded = $this->dao->getRegistration($saved->getId());
        $this->assertInstanceOf(WorkspaceNotificationRegistration::class, $reloaded);
        $this->assertSame('fresh', $reloaded->getChannelLabel());
        $this->assertSame('UTC', $reloaded->getTimezone());
        $this->assertSame('09:00', $reloaded->getDailySendTime());
        $this->assertSame(WorkspaceNotificationRegistration::PROVIDER_SLACK, $reloaded->getProvider());
        $this->assertTrue($reloaded->isActive());
    }

    public function testSaveRegistrationPersistsSubunitsJoinTable(): void
    {
        $reg = $this->makeRegistration('LEAVE_TODAY', 'with-subs');
        $reg->addSubunit($this->subunitRef(2));
        $reg->addSubunit($this->subunitRef(3));
        $this->dao->saveRegistration($reg);

        $this->getEntityManager()->clear();
        $reloaded = $this->dao->getRegistration($reg->getId());
        $ids = [];
        foreach ($reloaded->getSubunits() as $subunit) {
            $ids[] = $subunit->getId();
        }
        sort($ids);
        $this->assertSame([2, 3], $ids);
    }

    public function testSaveRegistrationCanReplaceSubunitsOnSubsequentSave(): void
    {
        $reg = $this->makeRegistration('LEAVE_TODAY', 'will-mutate');
        $reg->addSubunit($this->subunitRef(2));
        $reg->addSubunit($this->subunitRef(3));
        $this->dao->saveRegistration($reg);
        $id = $reg->getId();

        $this->getEntityManager()->clear();
        $managed = $this->dao->getRegistration($id);
        $this->assertNotNull($managed);
        $managed->clearSubunits();
        $managed->addSubunit($this->subunitRef(4));
        $this->dao->saveRegistration($managed);

        $this->getEntityManager()->clear();
        $reloaded = $this->dao->getRegistration($id);
        $ids = [];
        foreach ($reloaded->getSubunits() as $subunit) {
            $ids[] = $subunit->getId();
        }
        $this->assertSame([4], $ids, 'Old join rows must be replaced, not merged');
    }

    public function testDeleteRegistrationRemovesRow(): void
    {
        $reg = $this->saveBasicRegistration('BIRTHDAY', 'doomed');
        $id = $reg->getId();

        $this->dao->deleteRegistration($reg);
        $this->getEntityManager()->clear();

        $this->assertNull($this->dao->getRegistration($id));
    }

    public function testDeleteRegistrationCascadesJoinTable(): void
    {
        $reg = $this->makeRegistration('BIRTHDAY', 'with-subs-doomed');
        $reg->addSubunit($this->subunitRef(2));
        $this->dao->saveRegistration($reg);
        $id = $reg->getId();

        $this->dao->deleteRegistration($reg);
        $this->getEntityManager()->clear();

        $count = (int)$this->getEntityManager()
            ->getConnection()
            ->fetchOne(
                'SELECT COUNT(*) FROM ohrm_workspace_notification_registration_subunit WHERE registration_id = :id',
                ['id' => $id]
            );
        $this->assertSame(0, $count, 'Cascade must clean the join rows');
    }

    public function testDeleteRegistrationsNotInKeepsOnlyListedIds(): void
    {
        $keepA = $this->saveBasicRegistration('BIRTHDAY', 'keep-a');
        $keepB = $this->saveBasicRegistration('LEAVE_TODAY', 'keep-b');
        $drop = $this->saveBasicRegistration('BIRTHDAY', 'drop');

        $this->dao->deleteRegistrationsNotIn([$keepA->getId(), $keepB->getId()]);
        $this->getEntityManager()->clear();

        $remaining = array_map(fn ($r) => $r->getId(), $this->dao->listRegistrations(new WorkspaceNotificationRegistrationSearchFilterParams()));
        sort($remaining);
        $expected = [$keepA->getId(), $keepB->getId()];
        sort($expected);
        $this->assertSame($expected, $remaining);
        $this->assertNull($this->dao->getRegistration($drop->getId()));
    }

    private function makeRegistration(string $eventType, string $channelLabel, bool $active = true): WorkspaceNotificationRegistration
    {
        $reg = new WorkspaceNotificationRegistration();
        $reg->setProvider(WorkspaceNotificationRegistration::PROVIDER_SLACK);
        $reg->setEventType($eventType);
        $reg->setWebhookUrl('encrypted-blob');
        $reg->setChannelLabel($channelLabel);
        $reg->setTimezone('UTC');
        $reg->setDailySendTime('09:00');
        $reg->setActive($active);
        return $reg;
    }

    private function saveBasicRegistration(string $eventType, string $channelLabel, bool $active = true): WorkspaceNotificationRegistration
    {
        return $this->dao->saveRegistration($this->makeRegistration($eventType, $channelLabel, $active));
    }

    private function subunitRef(int $id): Subunit
    {
        return $this->getEntityReference(Subunit::class, $id);
    }
}
