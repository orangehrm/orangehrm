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

namespace OrangeHRM\Tests\LDAP;

use Closure;
use Countable;
use Exception;
use Symfony\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;

class LDAPUsersFixture
{
    private AdapterInterface $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function load(): void
    {
        $this->createRootOrgUnits();

        $this->createAdminOrgUnit();
        $this->createSalesOrgUnit();
        $this->createLegalOrgUnit();
        $this->createEngineeringOrgUnit();
        $this->createFinanceOrgUnit();

        $this->createAdminsGroup();
        $this->createManagersGroup();
        $this->createEmployeesGroup();
    }

    public function clean(): void
    {
        $this->deleteAdminsGroup();
        $this->deleteManagersGroup();
        $this->deleteEmployeesGroup();

        $this->deleteAdminOrgUnit();
        $this->deleteSalesOrgUnit();
        $this->deleteLegalOrgUnit();
        $this->deleteEngineeringOrgUnit();
        $this->deleteFinanceOrgUnit();

        $this->deleteRootOrgUnits();
    }

    /**
     * @param string $dn
     * @param string $query
     */
    private function searchAndDelete(string $dn, string $query = 'objectClass=*'): void
    {
        $this->tryWrapper(function () use ($dn, $query) {
            $results = $this->adapter->createQuery($dn, $query)
                ->execute()
                ->toArray();
            foreach ($results as $entry) {
                $this->adapter->getEntryManager()->remove($entry);
            }
        });
    }

    /**
     * @param Entry $entry
     */
    private function removeEntity(Entry $entry): void
    {
        $this->tryWrapper(function () use ($entry) {
            $this->adapter->getEntryManager()->remove($entry);
        });
    }

    /**
     * @param Closure $closure
     */
    private function tryWrapper(Closure $closure): void
    {
        try {
            $closure();
        } catch (LdapException $e) {
        }
    }

    protected function createRootOrgUnits(): void
    {
        try {
            $results = $this->adapter
                ->createQuery('dc=example,dc=org', '(&(objectClass=dcObject)(objectClass=organization))')
                ->execute();
        } catch (LdapException $e) {
            $results = new class () implements Countable {
                public function count(): int
                {
                    return 0;
                }
            };
        }
        if ($results->count() == 0) {
            throw new Exception('`dc=example,dc=org` root not created');
        }

        $entry = new Entry('ou=users,dc=example,dc=org', ['objectClass' => ['organizationalUnit', 'top']]);
        $this->adapter->getEntryManager()->add($entry);

        $entry = new Entry('ou=groups,dc=example,dc=org', ['objectClass' => ['organizationalUnit', 'top']]);
        $this->adapter->getEntryManager()->add($entry);
    }

    protected function deleteRootOrgUnits(): void
    {
        $entry = new Entry('ou=groups,dc=example,dc=org');
        $this->removeEntity($entry);

        $this->searchAndDelete('ou=users,dc=example,dc=org', '(|(objectClass=inetOrgPerson)(objectClass=person))');
        $this->searchAndDelete('ou=users,dc=example,dc=org', 'objectClass=groupOfNames');

        $entry = new Entry('ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
    }

    protected function createAdminOrgUnit(): void
    {
        $entry = new Entry('ou=admin,ou=users,dc=example,dc=org', ['objectClass' => ['organizationalUnit']]);
        $this->adapter->getEntryManager()->add($entry);
        $entry = new Entry('ou=hr,ou=admin,ou=users,dc=example,dc=org', ['objectClass' => ['organizationalUnit']]);
        $this->adapter->getEntryManager()->add($entry);

        $users = ['Linda.Anderson', 'Rebecca.Harmony', 'Lisa.Andrews', 'Jacqueline.White', 'Fiona.Grace'];
        foreach ($users as $user) {
            $names = explode('.', $user);
            $entry = new Entry(
                "uid=$user,ou=admin,ou=users,dc=example,dc=org",
                [
                    'objectClass' => [
                        'inetOrgPerson',
                        'organizationalPerson',
                        'person',
                        'top',
                    ],
                    'cn' => [$user],
                    'sn' => [$names[1]],
                    'givenName' => [$names[0]],
                    'displayName' => [implode(' ', $names)],
                    'userPassword' => [$user],
                ]
            );
            $this->adapter->getEntryManager()->add($entry);
        }

        $entry = new Entry(
            'uid=Abbey+sn=Kayla,ou=admin,ou=users,dc=example,dc=org',
            [
                'objectClass' => [
                    'inetOrgPerson',
                    'organizationalPerson',
                    'person',
                    'top',
                ],
                'cn' => ['Abbey'],
                'givenName' => ['Abbey'],
                'displayName' => ['Abbey Kayla'],
                'userPassword' => ['Abbey'],
            ]
        );
        $this->adapter->getEntryManager()->add($entry);

        $entry = new Entry(
            'cn=Paul+sn=Collings,ou=admin,ou=users,dc=example,dc=org',
            [
                'objectClass' => [
                    'inetOrgPerson',
                    'organizationalPerson',
                    'person',
                    'top',
                ],
                'givenName' => ['Paul'],
                'displayName' => ['Paul Collings'],
                'userPassword' => ['Paul'],
            ]
        );
        $this->adapter->getEntryManager()->add($entry);

        $entry = new Entry(
            'uid=bot,ou=admin,ou=users,dc=example,dc=org',
            ['objectClass' => ['account']]
        );
        $this->adapter->getEntryManager()->add($entry);

        $entry = new Entry(
            'cn=shared.laptop,ou=admin,ou=users,dc=example,dc=org',
            ['objectClass' => ['device']]
        );
        $this->adapter->getEntryManager()->add($entry);

        $users = ['David.Morris', 'Garry.White', 'Jasmine.Morgan', 'John.Smith', 'Kevin.Mathews'];
        foreach ($users as $user) {
            $names = explode('.', $user);
            $entry = new Entry(
                "uid=$user,ou=hr,ou=admin,ou=users,dc=example,dc=org",
                [
                    'objectClass' => [
                        'inetOrgPerson',
                        'organizationalPerson',
                        'person',
                        'top',
                    ],
                    'cn' => [$user],
                    'sn' => [$names[1]],
                    'givenName' => [$names[0]],
                    'displayName' => [implode(' ', $names)],
                    'userPassword' => [$user],
                ]
            );
            $this->adapter->getEntryManager()->add($entry);
        }
    }

    protected function deleteAdminOrgUnit(): void
    {
        $this->searchAndDelete(
            'ou=admin,ou=users,dc=example,dc=org',
            '(|(objectClass=person)(objectClass=account)(objectClass=device))'
        );
        $entry = new Entry('ou=hr,ou=admin,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
        $entry = new Entry('ou=admin,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
    }

    protected function createSalesOrgUnit(): void
    {
        $entry = new Entry('ou=sales,ou=users,dc=example,dc=org', ['objectClass' => ['organizationalUnit']]);
        $this->adapter->getEntryManager()->add($entry);
        $entry = new Entry(
            'ou=marketing,ou=sales,ou=users,dc=example,dc=org',
            ['objectClass' => ['organizationalUnit']]
        );
        $this->adapter->getEntryManager()->add($entry);

        $users = ['abbey', 'peter', 'david'];
        foreach ($users as $user) {
            $entry = new Entry(
                "mail=$user@example.org,ou=marketing,ou=sales,ou=users,dc=example,dc=org",
                [
                    'objectClass' => [
                        'inetOrgPerson',
                        'organizationalPerson',
                        'person',
                        'top',
                    ],
                    'cn' => [$user],
                    'sn' => [$user],
                    'userPassword' => [$user],
                ]
            );
            $this->adapter->getEntryManager()->add($entry);
        }
        $entry = new Entry(
            'ou=client services,ou=sales,ou=users,dc=example,dc=org',
            ['objectClass' => ['organizationalUnit']]
        );
        $this->adapter->getEntryManager()->add($entry);

        $users = [
            ['#Aaliyah+Haq', 'Aaliyah', 'Haq'],
            ['Amar;(Anthony)', 'Amar', 'Anthony'],
            ['Anthony\/Nolan', 'Anthony', 'Nolan'],
            ['Cassidy!:Hope', 'Cassidy\\', 'Hope'],
            ['Charlie<Carter>', 'Charlie', 'Carter'],
            ['Chenzira.Chuki', 'Chenzira', 'Chuki'],
            ['James="Jim"-Smith', 'James', 'Smith, III'],
            ['Ehioze\'Ebo', 'Ehioze', 'Ebo'],
            ['Joe,Root', 'Joe', 'Root'],
            ['Jordan+uid=Mathews', 'Jordan', 'Mathews'],
            ['Luke,ou=Wright', 'Luke', 'Wright'],
            ['Jadine Jackie', 'Jadine', 'Jackie'],
        ];
        foreach ($users as $user) {
            $cn = $this->adapter->escape($user[0]);
            $entry = new Entry(
                "cn=$cn,ou=client services,ou=sales,ou=users,dc=example,dc=org",
                [
                    'objectClass' => [
                        'inetOrgPerson',
                        'organizationalPerson',
                        'person',
                    ],
                    'sn' => [$user[2]],
                    'givenName' => [$user[1]],
                    'displayName' => [implode(' ', [$user[1], $user[2]])],
                    'userPassword' => [$user[0]],
                ]
            );
            $this->adapter->getEntryManager()->add($entry);
        }
    }

    protected function deleteSalesOrgUnit(): void
    {
        $this->searchAndDelete('ou=sales,ou=users,dc=example,dc=org', 'objectClass=person');
        $entry = new Entry('ou=marketing,ou=sales,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
        $entry = new Entry('ou=client services,ou=sales,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
        $entry = new Entry('ou=sales,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
    }

    protected function createLegalOrgUnit(): void
    {
        $entry = new Entry('ou=legal,ou=users,dc=example,dc=org', ['objectClass' => ['organizationalUnit']]);
        $this->adapter->getEntryManager()->add($entry);
    }

    protected function deleteLegalOrgUnit(): void
    {
        $entry = new Entry('ou=legal,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
    }

    protected function createEngineeringOrgUnit(): void
    {
        $entry = new Entry('ou=engineering,ou=users,dc=example,dc=org', ['objectClass' => ['organizationalUnit']]);
        $this->adapter->getEntryManager()->add($entry);
        $entry = new Entry(
            'ou=developers,ou=engineering,ou=users,dc=example,dc=org',
            ['objectClass' => ['organizationalUnit']]
        );
        $this->adapter->getEntryManager()->add($entry);
        $entry = new Entry(
            'ou=managers,ou=engineering,ou=users,dc=example,dc=org',
            ['objectClass' => ['organizationalUnit']]
        );
        $this->adapter->getEntryManager()->add($entry);
        $entry = new Entry(
            'ou=qa,ou=engineering,ou=users,dc=example,dc=org',
            ['objectClass' => ['organizationalUnit']]
        );
        $this->adapter->getEntryManager()->add($entry);

        for ($i = 1; $i <= 1000; $i++) {
            $userId = "user$i";
            $entry = new Entry(
                "uid=$userId,ou=developers,ou=engineering,ou=users,dc=example,dc=org",
                [
                    'objectClass' => [
                        'inetOrgPerson',
                        'organizationalPerson',
                        'person',
                        'top',
                    ],
                    'cn' => ["User $i"],
                    'sn' => ["Last$i"],
                    'givenName' => ["First$i"],
                    'displayName' => ["First$i Last$i"],
                    'userPassword' => [$userId],
                ]
            );
            $this->adapter->getEntryManager()->add($entry);
        }

        for ($i = 1; $i <= 100; $i++) {
            $userId = "user$i";
            $entry = new Entry(
                "uid=$userId,ou=qa,ou=engineering,ou=users,dc=example,dc=org",
                [
                    'objectClass' => [
                        'inetOrgPerson',
                        'organizationalPerson',
                        'person',
                        'top',
                    ],
                    'cn' => ["User $i"],
                    'sn' => ["Last$i"],
                    'givenName' => ["First$i"],
                    'displayName' => ["First$i Last$i"],
                    'userPassword' => [$userId],
                ]
            );
            $this->adapter->getEntryManager()->add($entry);
        }

        $users = ['Odis.Adalwin', 'Peter.Anderson', 'Russel.Hamilton'];
        foreach ($users as $user) {
            $names = explode('.', $user);
            $entry = new Entry(
                "uid=$user,ou=managers,ou=engineering,ou=users,dc=example,dc=org",
                [
                    'objectClass' => [
                        'inetOrgPerson',
                        'organizationalPerson',
                        'person',
                        'top',
                    ],
                    'cn' => [$user],
                    'sn' => [$names[1]],
                    'givenName' => [$names[0]],
                    'displayName' => [implode(' ', $names)],
                    'userPassword' => [$user],
                ]
            );
            $this->adapter->getEntryManager()->add($entry);
        }
    }

    protected function deleteEngineeringOrgUnit(): void
    {
        $this->searchAndDelete('ou=engineering,ou=users,dc=example,dc=org', 'objectClass=person');
        $entry = new Entry('ou=developers,ou=engineering,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
        $entry = new Entry('ou=managers,ou=engineering,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
        $entry = new Entry('ou=qa,ou=engineering,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
        $entry = new Entry('ou=engineering,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
    }

    protected function createFinanceOrgUnit(): void
    {
        $entry = new Entry('ou=finance,ou=users,dc=example,dc=org', ['objectClass' => ['organizationalUnit']]);
        $this->adapter->getEntryManager()->add($entry);

        $users = ['Sara.Tencrady', 'Peter.Anderson', 'Linda.Anderson'];
        foreach ($users as $user) {
            $names = explode('.', $user);
            $entry = new Entry(
                "cn=$user,ou=finance,ou=users,dc=example,dc=org",
                [
                    'objectClass' => [
                        'inetOrgPerson',
                        'organizationalPerson',
                        'person',
                        'top',
                    ],
                    'uid' => [$user],
                    'sn' => [$names[1]],
                    'givenName' => [$names[0]],
                    'displayName' => [implode(' ', $names)]
                ]
            );
            $this->adapter->getEntryManager()->add($entry);
        }
    }

    protected function deleteFinanceOrgUnit(): void
    {
        $this->searchAndDelete('ou=finance,ou=users,dc=example,dc=org', 'objectClass=person');
        $entry = new Entry('ou=finance,ou=users,dc=example,dc=org');
        $this->removeEntity($entry);
    }

    protected function createAdminsGroup(): void
    {
        $entry = new Entry(
            'cn=admins,ou=groups,dc=example,dc=org',
            [
                'objectClass' => ['groupOfNames', 'top'],
                'member' => ['ou=admin,ou=users,dc=example,dc=org'],
            ]
        );
        $this->adapter->getEntryManager()->add($entry);
    }

    protected function deleteAdminsGroup(): void
    {
        $entry = new Entry('cn=admins,ou=groups,dc=example,dc=org');
        $this->removeEntity($entry);
    }

    protected function createManagersGroup(): void
    {
        $entry = new Entry(
            'cn=managers,ou=groups,dc=example,dc=org',
            [
                'objectClass' => ['groupOfUniqueNames', 'top'],
                'uniqueMember' => [
                    'uid=Odis.Adalwin,ou=managers,ou=engineering,ou=users,dc=example,dc=org',
                    'uid=Peter.Anderson,ou=managers,ou=engineering,ou=users,dc=example,dc=org',
                ],
            ]
        );
        $this->adapter->getEntryManager()->add($entry);
    }

    protected function deleteManagersGroup(): void
    {
        $entry = new Entry('cn=managers,ou=groups,dc=example,dc=org');
        $this->removeEntity($entry);
    }

    protected function createEmployeesGroup(): void
    {
        $entry = new Entry(
            'cn=employees+ou=engineering,ou=groups,dc=example,dc=org',
            [
                'objectClass' => ['groupOfUniqueNames', 'top'],
                'uniqueMember' => [
                    'ou=developers,ou=engineering,ou=users,dc=example,dc=org',
                    'ou=qa,ou=engineering,ou=users,dc=example,dc=org',
                ],
            ]
        );
        $this->adapter->getEntryManager()->add($entry);
    }

    protected function deleteEmployeesGroup(): void
    {
        $entry = new Entry('cn=employees+ou=engineering,ou=groups,dc=example,dc=org');
        $this->removeEntity($entry);
    }
}
