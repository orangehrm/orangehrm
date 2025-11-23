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

namespace OrangeHRM\Tests\Util\Fixture;

use Doctrine\ORM\EntityManager;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\TextHelperService;
use OrangeHRM\ORM\Doctrine;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractFixture
{
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

    public function dumpFixture(): void
    {
        $database = $this->getEntityManager()->getConnection()->getDatabase();
        if ($this->getTextHelperService()->strStartsWith($database, 'test_')) {
            throw new Exception('Invalid database connection');
        }

        $pathToFixturesDirectory = realpath(Config::get(Config::TEST_DIR) . '/phpunit/fixtures');
        $content = Yaml::dump($this->getContent());
        file_put_contents($pathToFixturesDirectory . DIRECTORY_SEPARATOR . $this->getFileName(), $content);
    }

    /**
     * @return array
     */
    abstract protected function getContent(): array;

    /**
     * @return string
     */
    abstract public static function getFileName(): string;
}
