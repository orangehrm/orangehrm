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

namespace OrangeHRM\Tests\Util;

use Doctrine\ORM\EntityManager;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\TextHelperService;
use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\DataGroupPermission;
use OrangeHRM\Entity\DisplayField;
use OrangeHRM\Entity\FilterField;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\Entity\ReportGroup;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\Fixture\AbstractFixture;
use OrangeHRM\Tests\Util\Fixture\CountryFixture;
use OrangeHRM\Tests\Util\Fixture\DataGroupPermissionFixture;
use OrangeHRM\Tests\Util\Fixture\DisplayFieldFixture;
use OrangeHRM\Tests\Util\Fixture\FilterFieldFixture;
use OrangeHRM\Tests\Util\Fixture\I18NLangStringFixture;
use OrangeHRM\Tests\Util\Fixture\MenuItemFixture;
use OrangeHRM\Tests\Util\Fixture\NationalityFixture;
use OrangeHRM\Tests\Util\Fixture\ReportGroupFixture;
use OrangeHRM\Tests\Util\Fixture\WorkflowStateMachineFixture;

class CoreFixtureService
{
    public const REGISTERED_FIXTURES = [
        Country::class => CountryFixture::class,
        Nationality::class => NationalityFixture::class,
        ReportGroup::class => ReportGroupFixture::class,
        FilterField::class => FilterFieldFixture::class,
        DisplayField::class => DisplayFieldFixture::class,
        DataGroupPermission::class => DataGroupPermissionFixture::class,
        WorkflowStateMachine::class => WorkflowStateMachineFixture::class,
        MenuItem::class => MenuItemFixture::class,
        I18NLangString::class => I18NLangStringFixture::class,
    ];

    private ?TextHelperService $textHelperService = null;

    /**
     * @return TextHelperService
     */
    protected function getTextHelperService(): TextHelperService
    {
        if (!$this->textHelperService instanceof TextHelperService) {
            $this->textHelperService = new TextHelperService();
        }
        return $this->textHelperService;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return Doctrine::getEntityManager();
    }

    public function saveToFixtures()
    {
        $database = $this->getEntityManager()->getConnection()->getDatabase();
        if ($this->getTextHelperService()->strStartsWith($database, 'test_')) {
            throw new Exception('Invalid database connection');
        }
        foreach (self::REGISTERED_FIXTURES as $fixtureClassName) {
            $fixtureClass = new $fixtureClassName();
            if ($fixtureClass instanceof AbstractFixture) {
                $fixtureClass->dumpFixture();
            }
        }
    }

    /**
     * @return bool
     */
    public function isReady(): bool
    {
        $pathToFixturesDirectory = realpath(Config::get(Config::TEST_DIR) . '/phpunit/fixtures');
        foreach (self::REGISTERED_FIXTURES as $fixtureClassName) {
            $filename = $fixtureClassName::getFileName();
            $file = $pathToFixturesDirectory . DIRECTORY_SEPARATOR . $filename;
            if (!file_exists($file)) {
                return false;
            }
        }
        return true;
    }
}
