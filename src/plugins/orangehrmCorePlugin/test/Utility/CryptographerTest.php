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

namespace OrangeHRM\Tests\Core\Utility;

use OrangeHRM\Core\Utility\Cryptographer;
use OrangeHRM\Tests\Util\TestCase;

class CryptographerTest extends TestCase
{
    private Cryptographer $cryptographer;

    protected function setUp(): void
    {
        $key = "5bd247dff9833976c465e8d037debabfd6faf5e385f988512db46682a558757002465a8f4cab240ed9005f1f99b1a2879afcc67226a4c7bd110b495adb76d967";
        $this->cryptographer = new Cryptographer($key);
    }

    public function testEncrypt(): void
    {
        $value = "1234";
        $expected = "31943CEFE2B2ABC03E4B8A0665D79AD0";

        $this->assertEquals($expected, $this->cryptographer->encrypt($value));
    }

    public function testDecrypt(): void
    {
        $value = "31943CEFE2B2ABC03E4B8A0665D79AD0";
        $expected = "1234";

        $this->assertEquals($expected, $this->cryptographer->decrypt($value));
    }

    public function testEncryptDecrypt(): void
    {
        $value = "Test";
        $encrypted = $this->cryptographer->encrypt($value);

        $this->assertEquals($value, $this->cryptographer->decrypt($encrypted));
    }
}
