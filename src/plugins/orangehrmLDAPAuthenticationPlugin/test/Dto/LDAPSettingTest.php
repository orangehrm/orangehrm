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

namespace OrangeHRM\Tests\LDAP\Dto;

use InvalidArgumentException;
use OrangeHRM\LDAP\Dto\LDAPSetting;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Admin
 * @group LDAP
 * @group Dto
 */
class LDAPSettingTest extends TestCase
{
    public function testFromString(): void
    {
        $setting = new LDAPSetting('example.com', 1389, 'OpenLDAP', 'tls');
        $setting->setBindAnonymously(true);
        $this->assertEquals(
            '{"enable":false,"host":"example.com","port":1389,"encryption":"tls","implementation":"OpenLDAP","version":"3","optReferrals":false,"bindAnonymously":true,"bindUserDN":null,"bindUserPassword":null,"userLookupSettings":[],"dataMapping":{"firstName":"givenName","middleName":null,"lastName":"sn","workEmail":null,"employeeId":null,"userStatus":null},"mergeLDAPUsersWithExistingSystemUsers":false,"syncInterval":1}',
            (string)$setting
        );
        $this->expectException(InvalidArgumentException::class);
        new LDAPSetting('example.com', 1389, 'OpenLDAP', 'invalid');
    }
}
