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

namespace OrangeHRM\Installer\Util\SystemConfig;

use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\ConfigHelper;
use OrangeHRM\Installer\Util\Connection;

class SystemConfiguration
{
    public const NOT_CAPTURED = 'Not Captured';
    public const DEFAULT_LANGUAGE = 'en_US';

    public const INSTANCE_IDENTIFIER = "instance.identifier";
    public const INSTANCE_IDENTIFIER_CHECKSUM = "instance.identifier_checksum";

    private ?ConfigHelper $configHelper = null;
    private ?int $adminEmpNumber = null;

    public function __construct()
    {
        $this->configHelper = new ConfigHelper();
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function getConnection(): \Doctrine\DBAL\Connection
    {
        return Connection::getConnection();
    }

    /**
     * @return AbstractSchemaManager
     * @throws Exception
     */
    private function getSchemaManager(): AbstractSchemaManager
    {
        return $this->getConnection()->createSchemaManager();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getOrganizationName(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $organizationName = $qb->select('organization.name')
            ->from('ohrm_organization_gen_info', 'organization')
            ->fetchOne();
        return $organizationName ?: self::NOT_CAPTURED;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCountry(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $country = $qb->select('organization.country')
            ->from('ohrm_organization_gen_info', 'organization')
            ->fetchOne();
        return $country ?: self::NOT_CAPTURED;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        $result = $this->configHelper->getConfigValue('admin.localization.default_language');
        return empty($result) ? self::DEFAULT_LANGUAGE : $result;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAdminFirstName(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $result = $qb->select('employee.emp_firstname AS firstName')
            ->from('hs_hr_employee', 'employee')
            ->where('employee.emp_number = :empNumber')
            ->setParameter('empNumber', $this->getAdminEmployeeNumber())
            ->fetchOne();

        return $result ?: 'Admin';
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAdminLastName(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        return $qb->select('employee.emp_lastname AS lastName')
            ->from('hs_hr_employee', 'employee')
            ->where('employee.emp_number = :empNumber')
            ->setParameter('empNumber', $this->getAdminEmployeeNumber())
            ->fetchOne();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAdminEmail(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        return $qb->select('employee.emp_work_email AS email')
            ->from('hs_hr_employee', 'employee')
            ->where('employee.emp_number = :empNumber')
            ->setParameter('empNumber', $this->getAdminEmployeeNumber())
            ->fetchOne();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAdminContactNumber(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $result = $qb->select('employee.emp_work_telephone AS tpNumber')
            ->from('hs_hr_employee', 'employee')
            ->where('employee.emp_number = :empNumber')
            ->setParameter('empNumber', $this->getAdminEmployeeNumber())
            ->fetchOne();
        return $result ?: self::NOT_CAPTURED;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAdminUserName(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        return $qb->select('user.user_name AS userName')
            ->from('ohrm_user', 'user')
            ->where('user.emp_number = :empNumber')
            ->setParameter('empNumber', $this->getAdminEmployeeNumber())
            ->fetchOne();
    }

    /**
     * @return string|null
     */
    public function getInstanceIdentifier(): ?string
    {
        return $this->configHelper->getConfigValue(
            self::INSTANCE_IDENTIFIER
        );
    }

    /**
     * @return string|null
     */
    public function getInstanceIdentifierChecksum(): ?string
    {
        return $this->configHelper->getConfigValue(
            self::INSTANCE_IDENTIFIER_CHECKSUM
        );
    }

    /**
     * @return int|null
     * @throws Exception
     */
    private function getAdminEmployeeNumber(): ?int
    {
        if (is_null($this->adminEmpNumber)) {
            $qb = $this->getConnection()->createQueryBuilder();
            $qb->select('user.emp_number')
                ->from('ohrm_user', 'user');
            $this->adminEmpNumber = $qb->andWhere($qb->expr()->isNull('user.created_by'))
                ->setMaxResults(1)
                ->fetchOne();
        }
        return $this->adminEmpNumber;
    }

    /**
     * @param string $organizationName
     * @param string $email
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $host
     * @param string $country
     * @param string $ohrmVersion
     * @param int $currentTimestamp
     */
    public function setInstanceIdentifier(
        string $organizationName,
        string $email,
        string $adminFirstName,
        string $adminLastName,
        string $host,
        string $country,
        string $ohrmVersion,
        int $currentTimestamp
    ): void {
        $instanceIdentifier = $this->createInstanceIdentifier(
            $organizationName,
            $email,
            $adminFirstName,
            $adminLastName,
            $host,
            $country,
            $ohrmVersion,
            $currentTimestamp,
        );
        $this->configHelper->setConfigValue(
            self::INSTANCE_IDENTIFIER,
            $instanceIdentifier
        );
    }

    /**
     * @param string $organizationName
     * @param string $email
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $host
     * @param string $country
     * @param string $ohrmVersion
     * @param int $currentTimestamp
     */
    public function setInstanceIdentifierChecksum(
        string $organizationName,
        string $email,
        string $adminFirstName,
        string $adminLastName,
        string $host,
        string $country,
        string $ohrmVersion,
        int $currentTimestamp
    ): void {
        $instanceIdentifierChecksum = $this->createInstanceIdentifierChecksum(
            $organizationName,
            $email,
            $adminFirstName,
            $adminLastName,
            $host,
            $country,
            $ohrmVersion,
            $currentTimestamp
        );
        $this->configHelper->setConfigValue(
            self::INSTANCE_IDENTIFIER_CHECKSUM,
            $instanceIdentifierChecksum
        );
    }

    /**
     * @param string $organizationName
     * @param string $email
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string|null $host
     * @param string|null $country
     * @param string $ohrmVersion
     * @param int $currentTimestamp
     * @return string
     */
    public function createInstanceIdentifier(
        string $organizationName,
        string $email,
        string $adminFirstName,
        string $adminLastName,
        ?string $host,
        ?string $country,
        string $ohrmVersion,
        int $currentTimestamp
    ): string {
        $host = !is_null($host) ? $host : '';
        $country = !is_null($country) ? $country : '';

        return base64_encode(
            $organizationName .
            '_' . $email .
            '_' . $adminFirstName .
            '_' . $adminLastName .
            '_' . $host .
            '_' . $country .
            '_' . $ohrmVersion .
            '_' . $currentTimestamp
        );
    }

    /**
     * @param string $organizationName
     * @param string $email
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string|null $host
     * @param string|null $country
     * @param string $ohrmVersion
     * @return string
     */
    public function createInstanceIdentifierChecksum(
        string $organizationName,
        string $email,
        string $adminFirstName,
        string $adminLastName,
        ?string $host,
        ?string $country,
        string $ohrmVersion,
        int $currentTimestamp
    ): string {
        $host = !is_null($host) ? $host : '';
        $country = !is_null($country) ? $country : '';

        $parameters = [
            'organizationName' => $organizationName,
            'organizationEmail' => $email,
            'adminFirstName' => $adminFirstName,
            'adminLastName' => $adminLastName,
            'host' => $host,
            'country' => $country,
            'ohrmVersion' => $ohrmVersion,
            'currentTimestamp' => $currentTimestamp
        ];

        return base64_encode(serialize($parameters));
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isRegistrationEventQueueAvailable(): bool
    {
        return $this->getSchemaManager()->tablesExist(['ohrm_registration_event_queue']);
    }

    /**
     * @param int $eventType
     * @param int $published
     * @param string|null $data
     */
    public function setRegistrationEventQueue(int $eventType, int $published, string $data = null)
    {
        $eventTime = new DateTime();

        $qb = $this->getConnection()->createQueryBuilder();
        $qb->insert('ohrm_registration_event_queue')
            ->setValue('event_type', ':eventType')
            ->setParameter('eventType', $eventType)
            ->setValue('published', ':published')
            ->setParameter('published', $published)
            ->setValue('event_time', ':eventTime')
            ->setParameter('eventTime', $eventTime, Types::DATETIME_MUTABLE);
        if ($published !== 0) {
            $qb->setValue('publish_time', ':publishTime')
                ->setParameter('publishTime', $eventTime, Types::DATETIME_MUTABLE);
        }
        if (!is_null($data)) {
            $qb->setValue('data', ':data')
                ->setParameter('data', $data);
        }
        $qb->executeQuery();
    }

    /**
     * @param int $eventType
     * @param int $published
     * @param string|null $data
     * @throws Exception
     */
    public function updateRegistrationEventQueue(int $eventType, int $published, string $data = null)
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $eventQueueId = $qb->select('eventQueue.id')
            ->from('ohrm_registration_event_queue', 'eventQueue')
            ->where('eventQueue.event_type  = :eventType')
            ->setParameter('eventType', $eventType)
            ->orderBy('eventQueue.id', 'DESC')
            ->setMaxResults(1)
            ->fetchOne();

        $qb = $this->getConnection()->createQueryBuilder();
        $qb->update('ohrm_registration_event_queue', 'eventQueue')
            ->set('eventQueue.published', ':published')
            ->setParameter('published', $published)
            ->set('eventQueue.publish_time', ':publishTime')
            ->setParameter('publishTime', new DateTime(), Types::DATETIME_MUTABLE);
        if (!is_null($data)) {
            $qb->set('data', ':data')
                ->setParameter('data', $data);
        }
        $qb->andWhere('eventQueue.id  = :eventQueueId')
            ->setParameter('eventQueueId', $eventQueueId)
            ->executeQuery();
    }
}
