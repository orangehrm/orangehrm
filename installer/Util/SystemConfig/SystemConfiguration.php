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

namespace OrangeHRM\Installer\Util\SystemConfig;

use DateTime;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\ConfigHelper;
use OrangeHRM\Installer\Util\Connection;

class SystemConfiguration
{
    public const NOT_CAPTURED = 'Not Captured';
    public const DEFAULT_LANGUAGE = 'en_US';

    public const INSTANCE_IDENTIFIER = 'instance.identifier';

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
     */
    private function getSchemaManager(): AbstractSchemaManager
    {
        return $this->getConnection()->createSchemaManager();
    }

    /**
     * @return string
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
     */
    public function getAdminEmail(): string
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $result = $qb->select('employee.emp_work_email AS email')
            ->from('hs_hr_employee', 'employee')
            ->where('employee.emp_number = :empNumber')
            ->setParameter('empNumber', $this->getAdminEmployeeNumber())
            ->fetchOne();
        return $result ?: self::NOT_CAPTURED;
    }

    /**
     * @return string
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
     * @return int|null
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
     * @param string|null $host
     * @param string $country
     * @param string $ohrmVersion
     * @param int $currentTimestamp
     */
    public function setInstanceIdentifier(
        string $organizationName,
        string $email,
        string $adminFirstName,
        string $adminLastName,
        ?string $host,
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
     * @return bool
     */
    public function isRegistrationEventQueueAvailable(): bool
    {
        return $this->getSchemaManager()->tablesExist(['ohrm_registration_event_queue']);
    }

    /**
     * @param int $eventType
     * @param bool $published
     * @param string|null $data
     * @param DateTime|null $eventTime
     */
    public function saveRegistrationEvent(
        int $eventType,
        bool $published,
        string $data = null,
        ?DateTime $eventTime = null
    ): void {
        $eventTime = $eventTime ?? new DateTime();

        $qb = $this->getConnection()->createQueryBuilder();
        $qb->insert('ohrm_registration_event_queue')
            ->setValue('event_type', ':eventType')
            ->setParameter('eventType', $eventType)
            ->setValue('published', ':published')
            ->setParameter('published', $published, Types::BOOLEAN)
            ->setValue('event_time', ':eventTime')
            ->setParameter('eventTime', $eventTime, Types::DATETIME_MUTABLE);
        if ($published) {
            $qb->setValue('publish_time', ':publishTime')
                ->setParameter('publishTime', $eventTime, Types::DATETIME_MUTABLE);
        }
        if (!is_null($data)) {
            $qb->setValue('data', ':data')
                ->setParameter('data', $data);
        }
        $qb->executeStatement();
    }
}
