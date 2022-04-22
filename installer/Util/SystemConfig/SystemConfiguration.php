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
use OrangeHRM\Installer\Util\ConfigHelper;
use OrangeHRM\Installer\Util\Connection;

class SystemConfiguration
{
    public const NOT_CAPTURED = 'Not Captured';

    public const INSTANCE_IDENTIFIER = "instance.identifier";
    public const INSTANCE_IDENTIFIER_CHECKSUM = "instance.identifier_checksum";

    private ?ConfigHelper $configHelper = null;

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
        return $this->configHelper->getConfigValue(
            'admin.localization.default_language',
            self::NOT_CAPTURED
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAdminFirstName(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        return $qb->select('employee.emp_firstname AS firstName')
            ->from('hs_hr_employee', 'employee')
            ->where('employee.emp_number = :empNumber')
            ->setParameter('empNumber', $this->getAdminEmployeeNumber())
            ->fetchOne();
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
     * @return string
     * @throws Exception
     */
    private function getAdminEmployeeNumber(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $adminRoleId = $qb->select('role.id')
            ->from('ohrm_user_role', 'role')
            ->where('role.name = :name')
            ->setParameter('name', 'Admin')
            ->setMaxResults(1)
            ->fetchOne();

        $qb = $this->getConnection()->createQueryBuilder();
        return $qb->select('user.emp_number AS empNumber')
            ->from('ohrm_user', 'user')
            ->where('user.user_role_id = :roleId')
            ->setParameter('roleId', $adminRoleId)
            ->fetchOne();
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getEmployeeCount(): int
    {
        $qb = $this->getConnection()->createQueryBuilder();
        return $qb->select('COUNT(employee.emp_number) as employeeCount')
            ->from('hs_hr_employee', 'employee')
            ->fetchOne();
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
    private function createInstanceIdentifier(
        string $organizationName,
        string $email,
        string $adminFirstName,
        string $adminLastName,
        ?string $host,
        ?string $country,
        string $ohrmVersion,
        int $currentTimestamp
    ): string {
        $host = !is_null($host) ?: '';
        $country = !is_null($country) ?: '';

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
    private function createInstanceIdentifierChecksum(
        string $organizationName,
        string $email,
        string $adminFirstName,
        string $adminLastName,
        ?string $host,
        ?string $country,
        string $ohrmVersion,
        int $currentTimestamp
    ): string {
        $host = !is_null($host) ?: '';
        $country = !is_null($country) ?: '';

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
     * @param int $eventType
     * @param int $published
     * @param string|null $data
     */
    public function setInitialRegistrationEventQueue(int $eventType, int $published, string $data = null)
    {
        $dateTime = new DateTime();
        $eventTime = $dateTime->format('Y-m-d H:i:s');

        $qb = $this->getConnection()->createQueryBuilder();
        $qb->insert('ohrm_registration_event_queue')
            ->setValue('event_type', ':eventType')
            ->setParameter('eventType', $eventType)
            ->setValue('published', ':published')
            ->setParameter('published', $published)
            ->setValue('event_time', ':eventTime')
            ->setParameter('eventTime', $eventTime);
        if ($published !== 0) {
            $qb->setValue('publish_time', ':publishTime')
                ->setParameter('publishTime', $eventTime);
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
    public function updateInitialRegistrationEventQueue(int $eventType, int $published, string $data = null)
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $eventQueueId = $qb->select('eventQueue.id')
            ->from('ohrm_registration_event_queue', 'eventQueue')
            ->where('eventQueue.event_type  = :eventType')
            ->setParameter('eventType', $eventType)
            ->orderBy('eventQueue.id', 'DESC')
            ->setMaxResults(1)
            ->fetchOne();

        $dateTime = new DateTime();
        $publishTime = $dateTime->format('Y-m-d H:i:s');

        $qb = $this->getConnection()->createQueryBuilder();
        $qb->update('ohrm_registration_event_queue', 'eventQueue')
            ->set('eventQueue.published', ':published')
            ->setParameter('published', $published)
            ->set('eventQueue.publish_time', ':publishTime')
            ->setParameter('publishTime', $publishTime);
        if (!is_null($data)) {
            $qb->set('data', ':data')
                ->setParameter('data', $data);
        }
        $qb->andWhere('eventQueue.id  = :eventQueueId')
            ->setParameter('eventQueueId', $eventQueueId)
            ->executeQuery();
    }
}
