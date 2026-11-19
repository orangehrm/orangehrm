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

namespace OrangeHRM\Tests\Core\Installer;

use OrangeHRM\Installer\Command\InstallerCommandHelperTrait;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Tests\Util\TestCase;
use ReflectionMethod;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * What the CLI installer and upgrader print about the application URL after `--baseUrl` handling.
 *
 * @group Core
 * @group Installer
 */
class InstallerCommandBaseUrlTest extends TestCase
{
    public function testGivenUrlIsStored(): void
    {
        $appSetupUtility = $this->getAppSetupUtility();
        $appSetupUtility->expects($this->once())
            ->method('saveInstanceBaseUrl')
            ->with('https://hr.example.org/web/index.php', true)
            ->willReturn(true);
        $appSetupUtility->expects($this->never())->method('getStoredInstanceBaseUrl');

        $output = $this->store($appSetupUtility, 'https://hr.example.org/web/index.php');

        $this->assertStringContainsString('Application URL stored: https://hr.example.org/web/index.php', $output);
    }

    public function testWithoutOptionTheKeptUrlIsPrinted(): void
    {
        $appSetupUtility = $this->getAppSetupUtility();
        $appSetupUtility->expects($this->never())->method('saveInstanceBaseUrl');
        $appSetupUtility->method('getStoredInstanceBaseUrl')
            ->willReturn('https://hr.example.com/orangehrm/web/index.php');

        $output = $this->store($appSetupUtility, null);

        $this->assertStringContainsString(
            'Application URL kept: https://hr.example.com/orangehrm/web/index.php',
            $output
        );
        $this->assertStringContainsString('php bin/console orangehrm:set-base-url', $output);
    }

    public function testWithoutOptionAndWithoutStoredUrl(): void
    {
        $appSetupUtility = $this->getAppSetupUtility();
        $appSetupUtility->method('getStoredInstanceBaseUrl')->willReturn(null);

        $output = $this->store($appSetupUtility, null);

        $this->assertStringContainsString('No application URL is stored', $output);
        $this->assertStringNotContainsString('kept', $output);
        $this->assertStringContainsString('php bin/console orangehrm:set-base-url', $output);
    }

    /**
     * @return AppSetupUtility&\PHPUnit\Framework\MockObject\MockObject
     */
    private function getAppSetupUtility(): AppSetupUtility
    {
        return $this->getMockBuilder(AppSetupUtility::class)
            ->onlyMethods(['saveInstanceBaseUrl', 'getStoredInstanceBaseUrl'])
            ->getMock();
    }

    private function store(AppSetupUtility $appSetupUtility, ?string $baseUrl): string
    {
        $output = new BufferedOutput();
        $command = new class (new SymfonyStyle(new ArrayInput([]), $output)) {
            use InstallerCommandHelperTrait;

            private SymfonyStyle $io;

            public function __construct(SymfonyStyle $io)
            {
                $this->io = $io;
            }

            protected function getIO(): SymfonyStyle
            {
                return $this->io;
            }
        };
        $method = new ReflectionMethod($command, 'storeInstanceBaseUrl');
        $method->setAccessible(true);
        $method->invoke($command, $appSetupUtility, $baseUrl);
        // SymfonyStyle wraps long notes; compare on one line
        return preg_replace('/\s+/', ' ', str_replace(' ! ', ' ', $output->fetch()));
    }
}
