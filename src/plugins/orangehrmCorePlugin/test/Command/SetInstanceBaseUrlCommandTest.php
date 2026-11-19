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

namespace OrangeHRM\Tests\Core\Command;

use OrangeHRM\Core\Command\SetInstanceBaseUrlCommand;
use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group Core
 * @group Command
 */
class SetInstanceBaseUrlCommandTest extends KernelTestCase
{
    private const BASE_URL = 'https://hr.example.com/orangehrm/index.php';

    protected function setUp(): void
    {
        $this->createKernelWithMockServices([Services::CONFIG_SERVICE => new ConfigService()]);
        $this->setStoredBaseUrl(null);
    }

    protected function tearDown(): void
    {
        $this->setStoredBaseUrl(null);
        parent::tearDown();
    }

    public function testCommandName(): void
    {
        $this->assertSame('orangehrm:set-base-url', (new SetInstanceBaseUrlCommand())->getCommandName());
    }

    public function testStoresNormalizedUrl(): void
    {
        $tester = new CommandTester(new SetInstanceBaseUrlCommand());

        $exitCode = $tester->execute(['url' => ' HTTPS://HR.Example.com:443/orangehrm/index.php/ ']);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertSame(self::BASE_URL, $this->getStoredBaseUrl());
        $this->assertStringContainsString('Application URL stored: ' . self::BASE_URL, $tester->getDisplay());
    }

    public function testReplacesStoredUrl(): void
    {
        $this->setStoredBaseUrl('https://old.example.com/index.php');
        $tester = new CommandTester(new SetInstanceBaseUrlCommand());

        $this->assertSame(Command::SUCCESS, $tester->execute(['url' => self::BASE_URL]));
        $this->assertSame(self::BASE_URL, $this->getStoredBaseUrl());
    }

    /**
     * @dataProvider invalidUrlDataProvider
     */
    public function testInvalidUrlChangesNothing(string $value): void
    {
        $this->setStoredBaseUrl(self::BASE_URL);
        $tester = new CommandTester(new SetInstanceBaseUrlCommand());

        $exitCode = $tester->execute(['url' => $value]);

        $this->assertSame(Command::INVALID, $exitCode);
        $this->assertSame(self::BASE_URL, $this->getStoredBaseUrl());
        $this->assertStringContainsString('Invalid application URL', $tester->getDisplay());
    }

    public function invalidUrlDataProvider(): array
    {
        return [
            'no scheme' => ['hr.example.com/orangehrm/index.php'],
            'not http(s)' => ['javascript:alert(1)'],
            'credentials' => ['https://user:pass@hr.example.com/index.php'],
            'query' => ['https://hr.example.com/index.php?next=1'],
            'empty' => ['  '],
        ];
    }

    public function testShowsStoredUrlWithoutArgument(): void
    {
        $this->setStoredBaseUrl(self::BASE_URL);
        $tester = new CommandTester(new SetInstanceBaseUrlCommand());

        $this->assertSame(Command::SUCCESS, $tester->execute([]));
        $this->assertStringContainsString('Application URL: ' . self::BASE_URL, $tester->getDisplay());
        $this->assertSame(self::BASE_URL, $this->getStoredBaseUrl());
    }

    public function testSaysWhenNoUrlIsStored(): void
    {
        $tester = new CommandTester(new SetInstanceBaseUrlCommand());

        $this->assertSame(Command::SUCCESS, $tester->execute([]));
        $this->assertStringContainsString('No application URL is stored', $tester->getDisplay());
        $this->assertNull($this->getStoredBaseUrl());
    }

    private function getStoredBaseUrl(): ?string
    {
        return (new ConfigDao())->getValue(ConfigService::KEY_INSTANCE_BASE_URL);
    }

    private function setStoredBaseUrl(?string $value): void
    {
        $this->getEntityManager()->getConnection()
            ->delete('hs_hr_config', ['name' => ConfigService::KEY_INSTANCE_BASE_URL]);
        if ($value !== null) {
            (new ConfigDao())->setValue(ConfigService::KEY_INSTANCE_BASE_URL, $value);
        }
    }
}
