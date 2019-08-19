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

class Base64UrlTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider base64dataProvider
     * @param string $source
     * @param string $base64url
     */
    public function testEncodeDecode($source, $base64url) {
        $base64urlEncoding = Base64Url::encode($source);
        $this->assertEquals($base64url, $base64urlEncoding);

        $decoded = Base64Url::decode($base64url);
        $this->assertEquals($source, $decoded);
    }

    public function base64dataProvider() {
        return [
            ['', ''],
            ['hello', 'aGVsbG8'],
            ['sdfa', 'c2RmYQ'],
            ['sdfdee', 'c2RmZGVl'],
            ['孟子對曰王何必曰利', '5a2f5a2Q5bCN5puw546L5L2V5b-F5puw5Yip'],
            ['12時間以上かけて、遠く」のヒースロー空港につくと', 'MTLmmYLplpPku6XkuIrjgYvjgZHjgabjgIHpgaDjgY_jgI3jga7jg5Ljg7zjgrnjg63jg7znqbrmuK_jgavjgaTjgY_jgag']

        ];
    }
}
