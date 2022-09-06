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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Tests\LDAP\Dto;

use OrangeHRM\LDAP\Dto\LDAPEmployeeSelectorMapping;
use OrangeHRM\LDAP\Dto\LDAPUser;
use OrangeHRM\LDAP\Dto\LDAPUserLookupSetting;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Admin
 * @group LDAP
 * @group Dto
 */
class LDAPUserTest extends TestCase
{
    public function testLDAPUser(): void
    {
        $ldapUser = new LDAPUser();
        $ldapUser->setUserDN('uid=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org');
        $ldapUser->setUsername('Linda.Anderson');
        $ldapUser->setUserUniqueId('e12bb022-c1f4-103c-802e-d9f8f9ce8904');
        $ldapUser->setFirstName('Linda');
        $ldapUser->setLastName('Anderson');
        $ldapUser->setWorkEmail('linda@example.org');
        $ldapUser->setEmployeeId('0001');

        $this->assertEquals(
            '4f3a32373a224f72616e676548524d5c4c4441505c44746f5c4c44415055736572223a393a7b733a363a2275736572444e223b733a35343a227569643d4c696e64612e416e646572736f6e2c6f753d61646d696e2c6f753d75736572732c64633d6578616d706c652c64633d6f7267223b733a383a22757365726e616d65223b733a31343a224c696e64612e416e646572736f6e223b733a31323a2275736572556e697175654964223b733a33363a2265313262623032322d633166342d313033632d383032652d643966386639636538393034223b733a31313a2275736572456e61626c6564223b623a313b733a393a2266697273744e616d65223b733a353a224c696e6461223b733a31303a226d6964646c654e616d65223b733a303a22223b733a383a226c6173744e616d65223b733a383a22416e646572736f6e223b733a31303a22656d706c6f7965654964223b733a343a2230303031223b733a393a22776f726b456d61696c223b733a31373a226c696e6461406578616d706c652e6f7267223b7d',
            bin2hex(serialize($ldapUser))
        );
    }

    public function testLDAPUserWithLookupSetting(): void
    {
        $ldapUser = new LDAPUser();
        $ldapUser->setUserDN('uid=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org');
        $ldapUser->setUsername('Linda.Anderson');
        $ldapUser->setUserUniqueId('e12bb022-c1f4-103c-802e-d9f8f9ce8904');
        $ldapUser->setFirstName('Linda');
        $ldapUser->setLastName('Anderson');
        $ldapUser->setWorkEmail('linda@example.org');
        $ldapUser->setEmployeeId('0001');

        $userLookupSetting = new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=org');
        $ldapUser->setUserLookupSetting($userLookupSetting);

        $this->assertEquals(
            '4f3a32373a224f72616e676548524d5c4c4441505c44746f5c4c44415055736572223a393a7b733a363a2275736572444e223b733a35343a227569643d4c696e64612e416e646572736f6e2c6f753d61646d696e2c6f753d75736572732c64633d6578616d706c652c64633d6f7267223b733a383a22757365726e616d65223b733a31343a224c696e64612e416e646572736f6e223b733a31323a2275736572556e697175654964223b733a33363a2265313262623032322d633166342d313033632d383032652d643966386639636538393034223b733a31313a2275736572456e61626c6564223b623a313b733a393a2266697273744e616d65223b733a353a224c696e6461223b733a31303a226d6964646c654e616d65223b733a303a22223b733a383a226c6173744e616d65223b733a383a22416e646572736f6e223b733a31303a22656d706c6f7965654964223b733a343a2230303031223b733a393a22776f726b456d61696c223b733a31373a226c696e6461406578616d706c652e6f7267223b7d',
            bin2hex(serialize($ldapUser))
        );

        $userLookupSetting = new LDAPUserLookupSetting('ou=admin,ou=users,dc=example,dc=org');
        $employeeSelectorMapping = LDAPEmployeeSelectorMapping::createFromArray(
            [['field' => 'workEmail', 'attributeName' => 'mail']]
        );
        $userLookupSetting->setEmployeeSelectorMapping($employeeSelectorMapping);
        $ldapUser->setUserLookupSetting($userLookupSetting);

        $this->assertEquals(
            '4f3a32373a224f72616e676548524d5c4c4441505c44746f5c4c44415055736572223a393a7b733a363a2275736572444e223b733a35343a227569643d4c696e64612e416e646572736f6e2c6f753d61646d696e2c6f753d75736572732c64633d6578616d706c652c64633d6f7267223b733a383a22757365726e616d65223b733a31343a224c696e64612e416e646572736f6e223b733a31323a2275736572556e697175654964223b733a33363a2265313262623032322d633166342d313033632d383032652d643966386639636538393034223b733a31313a2275736572456e61626c6564223b623a313b733a393a2266697273744e616d65223b733a353a224c696e6461223b733a31303a226d6964646c654e616d65223b733a303a22223b733a383a226c6173744e616d65223b733a383a22416e646572736f6e223b733a31303a22656d706c6f7965654964223b733a343a2230303031223b733a393a22776f726b456d61696c223b733a31373a226c696e6461406578616d706c652e6f7267223b7d',
            bin2hex(serialize($ldapUser))
        );
    }
}
