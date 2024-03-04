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

namespace OrangeHRM\LDAP\Dao;

use Doctrine\ORM\NonUniqueResultException;
use InvalidArgumentException;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LDAPSyncStatus;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserAuthProvider;
use OrangeHRM\LDAP\Dto\LDAPAuthProvider;
use OrangeHRM\LDAP\Dto\LDAPEmployeeSearchFilterParams;
use OrangeHRM\ORM\ListSorter;

class LDAPDao extends BaseDao
{
    /**
     * @return int[]
     */
    public function getEmpNumbersWhoHaveManyUsers(): array
    {
        $q = $this->createQueryBuilder(User::class, 'user');
        $q->andWhere('user.deleted = :deleted')
            ->setParameter('deleted', false);
        $q->select('IDENTITY(user.employee) as empNumber');
        $q->addOrderBy('empNumber');

        $q->addGroupBy('empNumber');
        $q->andHaving($q->expr()->gte($q->expr()->count('empNumber'), ':userCount'))
            ->setParameter('userCount', 2);

        return array_column($q->getQuery()->execute(), 'empNumber');
    }

    /**
     * This method directly used for LDAP auth provider, changes may result invalid logins
     * @param string $userName
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getNonLocalUserByUserName(string $userName): ?User
    {
        $q = $this->createQueryBuilder(User::class, 'user')
            ->leftJoin('user.employee', 'employee')
            ->leftJoin('user.authProviders', 'authProvider')
            ->select('user', 'authProvider');

        $q->andWhere('user.userName = :userName')
            ->setParameter('userName', $userName)
            ->andWhere('user.deleted = :deleted')
            ->setParameter('deleted', false)
            ->andWhere($q->expr()->isNull('user.userPassword'));
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $userName
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getUserByUserName(string $userName): ?User
    {
        $q = $this->createQueryBuilder(User::class, 'user')
            ->leftJoin('user.employee', 'employee')
            ->leftJoin('user.authProviders', 'authProvider')
            ->select('user', 'authProvider');

        $q->andWhere('user.userName = :userName')
            ->setParameter('userName', $userName)
            ->andWhere('user.deleted = :deleted')
            ->setParameter('deleted', false);
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $userUniqueId
     * @return UserAuthProvider|null
     * @throws NonUniqueResultException
     */
    public function getAuthProviderByUserUniqueId(string $userUniqueId): ?UserAuthProvider
    {
        $q = $this->createQueryBuilder(UserAuthProvider::class, 'provider')
            ->leftJoin('provider.user', 'user')
            ->select('provider', 'user');

        $q->andWhere('provider.ldapUserUniqueId = :userUniqueId')
            ->setParameter('userUniqueId', $userUniqueId);
        $q->andWhere('provider.type = :type')
            ->setParameter('type', UserAuthProvider::TYPE_LDAP);
        $q->andWhere('user.deleted = :deleted')
            ->setParameter('deleted', false);
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param LDAPEmployeeSearchFilterParams $ldapEmployeeSearchFilterParams
     * @return Employee|null
     * @throws NonUniqueResultException
     */
    public function getEmployee(LDAPEmployeeSearchFilterParams $ldapEmployeeSearchFilterParams): ?Employee
    {
        $q = $this->createQueryBuilder(Employee::class, 'e');
        if (!is_null($ldapEmployeeSearchFilterParams->getEmpNumber())) {
            $q->andWhere('e.empNumber = :empNumber')
                ->setParameter('empNumber', $ldapEmployeeSearchFilterParams->getEmpNumber());
        }
        if (!is_null($ldapEmployeeSearchFilterParams->getEmployeeId())) {
            $q->andWhere('e.employeeId = :employeeId')
                ->setParameter('employeeId', $ldapEmployeeSearchFilterParams->getEmployeeId());
        }
        if (!is_null($ldapEmployeeSearchFilterParams->getWorkEmail())) {
            $q->andWhere('e.workEmail = :workEmail')
                ->setParameter('workEmail', $ldapEmployeeSearchFilterParams->getWorkEmail());
        }
        if (!is_null($ldapEmployeeSearchFilterParams->getDrivingLicenseNo())) {
            $q->andWhere('e.drivingLicenseNo = :drivingLicenseNo')
                ->setParameter('drivingLicenseNo', $ldapEmployeeSearchFilterParams->getDrivingLicenseNo());
        }
        if (!is_null($ldapEmployeeSearchFilterParams->getOtherId())) {
            $q->andWhere('e.otherId = :otherId')
                ->setParameter('otherId', $ldapEmployeeSearchFilterParams->getOtherId());
        }
        if (!is_null($ldapEmployeeSearchFilterParams->getOtherEmail())) {
            $q->andWhere('e.otherEmail = :otherEmail')
                ->setParameter('otherEmail', $ldapEmployeeSearchFilterParams->getOtherEmail());
        }
        if (!is_null($ldapEmployeeSearchFilterParams->getSsnNumber())) {
            $q->andWhere('e.ssnNumber = :ssnNumber')
                ->setParameter('ssnNumber', $ldapEmployeeSearchFilterParams->getSsnNumber());
        }
        if (!is_null($ldapEmployeeSearchFilterParams->getSinNumber())) {
            $q->andWhere('e.sinNumber = :sinNumber')
                ->setParameter('sinNumber', $ldapEmployeeSearchFilterParams->getSinNumber());
        }

        if (is_null($q->getDQLPart('where'))) {
            throw new InvalidArgumentException(
                'At least one parameter should define in ' . LDAPEmployeeSearchFilterParams::class
            );
        }
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @return LDAPAuthProvider[]
     */
    public function getAllLDAPAuthProviders(): array
    {
        $select = 'NEW ' . LDAPAuthProvider::class .
            '(provider.ldapUserDN, user.id)';
        $q = $this->createQueryBuilder(UserAuthProvider::class, 'provider')
            ->leftJoin('provider.user', 'user')
            ->select($select);
        $q->andWhere('provider.type = :type')
            ->setParameter('type', UserAuthProvider::TYPE_LDAP);
        $q->andWhere('user.deleted = :deleted')
            ->setParameter('deleted', false);
        return $q->getQuery()->execute();
    }

    /**
     * @param LDAPSyncStatus $ldapSyncStatus
     * @return LDAPSyncStatus
     */
    public function saveLdapSyncStatus(LDAPSyncStatus $ldapSyncStatus): LDAPSyncStatus
    {
        $this->persist($ldapSyncStatus);
        return $ldapSyncStatus;
    }

    /**
     * @return LDAPSyncStatus|null
     */
    public function getLastLDAPSyncStatus(): ?LDAPSyncStatus
    {
        return $this->getRepository(LDAPSyncStatus::class)
            ->findOneBy(
                [],
                [CommonParams::PARAMETER_ID => ListSorter::DESCENDING]
            );
    }
}
