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

namespace OrangeHRM\Tests\Core\Essential;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerTrait;
use Exception;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class UniqueLangStringTest extends TestCase
{
    use EntityManagerTrait;

    public function testUniqueValue(): void
    {
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/LangString.yaml');
        $q = $this->getEntityManager()->createQueryBuilder();
        $q->select('langString.value')
            ->from(I18NLangString::class, 'langString')
            ->groupBy('langString.value')
            ->having('count(langString.value) > 1');

        $result = $q->getQuery()->execute();
        $duplicateStrings = implode(" ", array_column($result, 'unitId'));
        if (! empty($duplicateStrings)) {
            throw new Exception(
                "Following language strings are duplicated;\n\n" .
                $duplicateStrings. "\n\n" . str_repeat('_ ', 20)
            );
        }
        $this->assertTrue(true);
    }

    public function testUniqueUnitId(): void
    {
        TestDataService::populate(Config::get(Config::TEST_DIR) . '/phpunit/fixtures/LangString.yaml');
        $exemptionUnitIds = ['date_format','first_name', 'password', 'past_employee', 'report_to', 'username'];
        $q = $this->getEntityManager()->createQueryBuilder();
        $q->select('langString.unitId')
            ->from(I18NLangString::class, 'langString')
            ->groupBy('langString.unitId')
            ->andWhere($q->expr()->notIn('langString.unitId', $exemptionUnitIds))
            ->having('count(langString.unitId) > 1');

        $result = $q->getQuery()->execute();
        $duplicateStrings = implode(" ", array_column($result, 'unitId'));
        if (! empty($duplicateStrings)) {
            throw new Exception(
                "Following unit Ids already in use;\n\n" .
                 $duplicateStrings. "\n\n" . str_repeat('_ ', 20)
            );
        }
        $this->assertTrue(true);
    }
}
