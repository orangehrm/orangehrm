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

use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Logger\Logger;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Framework\Http\Session\MemorySessionStorage;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\Installer\Controller\Installer\Api\InstanceBaseUrlAPI as InstallerInstanceBaseUrlAPI;
use OrangeHRM\Installer\Controller\Upgrader\Api\ConfigFileAPI;
use OrangeHRM\Installer\Controller\Upgrader\Api\InstanceBaseUrlAPI;
use OrangeHRM\Installer\Controller\Installer\InstallerCompleteController;
use OrangeHRM\Installer\Controller\Upgrader\UpgraderCompleteController;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\StateContainer;
use OrangeHRM\Tests\Util\TestCase;
use ReflectionMethod;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * Optional "Application URL" field of the web installer/upgrader, and how the config-file step stores it.
 *
 * @group Core
 * @group Installer
 */
class InstanceBaseUrlAPITest extends TestCase
{
    private const UPGRADER_URI = 'https://hr.example.com/orangehrm/installer/index.php/upgrader/api/base-url';

    private ?object $previousSession = null;

    protected function setUp(): void
    {
        $container = ServiceContainer::getContainer();
        $this->previousSession = $container->has(Services::SESSION) ? $container->get(Services::SESSION) : null;
        $session = new Session(new MemorySessionStorage());
        $session->start();
        $container->set(Services::SESSION, $session);
    }

    protected function tearDown(): void
    {
        ServiceContainer::getContainer()->set(Services::SESSION, $this->previousSession);
    }

    public function testRoutes(): void
    {
        $routes = Yaml::parseFile(realpath(__DIR__ . '/../../../../../installer/config/routes.yaml'));

        $this->assertSame('/installer/api/base-url', $routes['api_installer_base_url']['path']);
        $this->assertSame(
            InstallerInstanceBaseUrlAPI::class . '::handle',
            $routes['api_installer_base_url']['controller']
        );
        $this->assertSame(['GET', 'POST'], $routes['api_installer_base_url']['methods']);
        $this->assertSame('/upgrader/api/base-url', $routes['api_upgrader_base_url']['path']);
        $this->assertSame(InstanceBaseUrlAPI::class . '::handle', $routes['api_upgrader_base_url']['controller']);
        $this->assertSame(['GET', 'POST'], $routes['api_upgrader_base_url']['methods']);
    }

    public function testGetRequestResponse(): void
    {
        $response = $this->execute(new InstallerInstanceBaseUrlAPI(), $this->request('GET'));

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame(
            [
                'data' => [
                    'baseUrl' => null,
                    'stored' => null,
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function testJsonPostRequestResponse(): void
    {
        $response = $this->execute(
            new InstanceBaseUrlAPI(),
            $this->jsonRequest('{"baseUrl":"HTTPS://HR.Example.org/orangehrm/web/index.php/"}')
        );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame(
            ['data' => ['baseUrl' => 'https://hr.example.org/orangehrm/web/index.php']],
            json_decode($response->getContent(), true)
        );
        $this->assertSame(
            'https://hr.example.org/orangehrm/web/index.php',
            StateContainer::getInstance()->getInstanceBaseUrl()
        );
    }

    public function testJsonPostInvalidRequestResponse(): void
    {
        $response = $this->execute(new InstanceBaseUrlAPI(), $this->jsonRequest('{"baseUrl":"other.example"}'));

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $body['error']['status']);
        $this->assertStringStartsWith('Invalid `baseUrl`: expected the OrangeHRM address', $body['error']['message']);
        $this->assertFalse(StateContainer::getInstance()->hasInstanceBaseUrl());
    }

    public function testHandleRefusesOnInstalledSystem(): void
    {
        // The test environment is installed (Conf.php exists): the installer API answers 502 like every installer route
        $response = (new InstanceBaseUrlAPI())->handle($this->request('POST', ['baseUrl' => 'https://other.example/']));

        $this->assertSame(Response::HTTP_BAD_GATEWAY, $response->getStatusCode());
        $this->assertFalse(StateContainer::getInstance()->hasInstanceBaseUrl());
    }

    public function testUpgraderStoredUrlNeedsDatabaseInfo(): void
    {
        $api = new InstanceBaseUrlAPI();
        $this->assertNull($this->invoke($api, 'getStoredInstanceBaseUrl'));
    }

    public function testUpgraderStoredUrlFromDatabase(): void
    {
        $this->storeDbInfo('upgrade_db');
        $api = $this->apiWithAppSetupUtility(
            fn ($mock) => $mock->method('getStoredInstanceBaseUrl')->willReturn('https://hr.example.org/web/index.php')
        );

        $this->assertSame('https://hr.example.org/web/index.php', $this->invoke($api, 'getStoredInstanceBaseUrl'));
    }

    public function testUpgraderStoredUrlIsNullWhenDatabaseFails(): void
    {
        $this->storeDbInfo('upgrade_db');
        $api = $this->apiWithAppSetupUtility(
            fn ($mock) => $mock->method('getStoredInstanceBaseUrl')
                ->willThrowException(new RuntimeException('no table'))
        );
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $logger->expects($this->once())->method('error')->with('no table');
        $hadLogger = ServiceContainer::getContainer()->has(Services::LOGGER);
        $previousLogger = $hadLogger ? ServiceContainer::getContainer()->get(Services::LOGGER) : null;
        ServiceContainer::getContainer()->set(Services::LOGGER, $logger);
        try {
            $this->assertNull($this->invoke($api, 'getStoredInstanceBaseUrl'));
        } finally {
            ServiceContainer::getContainer()->set(Services::LOGGER, $previousLogger);
        }
    }

    public function testEnteredUrlBelongsToTheSelectedDatabase(): void
    {
        $this->storeDbInfo('first_db');
        $this->call(
            new InstanceBaseUrlAPI(),
            $this->request('POST', ['baseUrl' => 'https://hr.example.org/web/index.php'])
        );
        $this->assertTrue(StateContainer::getInstance()->hasInstanceBaseUrl());

        // Admin goes back and picks another database: the entered value no longer applies
        $this->storeDbInfo('second_db');
        $this->assertFalse(StateContainer::getInstance()->hasInstanceBaseUrl());
        $get = $this->call(new InstallerInstanceBaseUrlAPI(), $this->request('GET'));
        $this->assertNull($get['body']['data']['baseUrl']);
    }

    public function testPostRejectsNonStringValue(): void
    {
        $result = $this->call(
            new InstanceBaseUrlAPI(),
            $this->request('POST', ['baseUrl' => ['https://hr.example.org']])
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $result['status']);
        $this->assertFalse(StateContainer::getInstance()->hasInstanceBaseUrl());
    }

    /**
     * The address is not guessed from the URL the installer is opened with (web/ or index.php may or may not
     * be part of it, depending on the deployment)
     */
    public function testGetDoesNotPrefillFromTheRequest(): void
    {
        $result = $this->call(new InstallerInstanceBaseUrlAPI(), $this->request('GET'));

        $this->assertSame(Response::HTTP_OK, $result['status']);
        $this->assertNull($result['body']['data']['baseUrl']);
        $this->assertNull($result['body']['data']['stored']);
        $this->assertArrayNotHasKey('detected', $result['body']['data']);
    }

    public function testUpgraderGetPrefillsStoredUrl(): void
    {
        $api = new class () extends InstanceBaseUrlAPI {
            protected function getStoredInstanceBaseUrl(): ?string
            {
                return 'https://hr.example.org/web/index.php';
            }
        };

        $result = $this->call($api, $this->request('GET'));

        $this->assertSame('https://hr.example.org/web/index.php', $result['body']['data']['baseUrl']);
        $this->assertSame('https://hr.example.org/web/index.php', $result['body']['data']['stored']);
    }

    public function testPostStoresNormalizedUrlInSession(): void
    {
        $result = $this->call(
            new InstanceBaseUrlAPI(),
            $this->request('POST', ['baseUrl' => ' HTTPS://HR.Example.org:443/orangehrm/web/index.php/ '])
        );

        $this->assertSame(Response::HTTP_OK, $result['status']);
        $this->assertSame('https://hr.example.org/orangehrm/web/index.php', $result['body']['data']['baseUrl']);
        $this->assertTrue(StateContainer::getInstance()->hasInstanceBaseUrl());
        $this->assertSame(
            'https://hr.example.org/orangehrm/web/index.php',
            StateContainer::getInstance()->getInstanceBaseUrl()
        );

        // The prefill now shows what was entered
        $get = $this->call(new InstanceBaseUrlAPI(), $this->request('GET'));
        $this->assertSame('https://hr.example.org/orangehrm/web/index.php', $get['body']['data']['baseUrl']);
    }

    public function testPostEmptyRecordsThatTheFieldWasLeftEmpty(): void
    {
        $result = $this->call(new InstanceBaseUrlAPI(), $this->request('POST', ['baseUrl' => '  ']));

        $this->assertSame(Response::HTTP_OK, $result['status']);
        $this->assertNull($result['body']['data']['baseUrl']);
        $this->assertTrue(StateContainer::getInstance()->hasInstanceBaseUrl());
        $this->assertNull(StateContainer::getInstance()->getInstanceBaseUrl());
    }

    /**
     * @dataProvider invalidUrlDataProvider
     */
    public function testPostRejectsInvalidUrl(string $value): void
    {
        $result = $this->call(new InstanceBaseUrlAPI(), $this->request('POST', ['baseUrl' => $value]));

        $this->assertSame(Response::HTTP_BAD_REQUEST, $result['status']);
        $this->assertStringContainsString('Invalid `baseUrl`', $result['body']['error']['message']);
        $this->assertFalse(StateContainer::getInstance()->hasInstanceBaseUrl());
    }

    public function invalidUrlDataProvider(): array
    {
        return [
            ['hr.example.com/orangehrm/web/index.php'],
            ['javascript:alert(1)'],
            ['https://user:pass@hr.example.com/web/index.php'],
            ['https://hr.example.com/web/index.php?next=1'],
        ];
    }

    public function testConfigFileStepStoresEnteredUrlAndMayReplace(): void
    {
        StateContainer::getInstance()->storeInstanceBaseUrl('https://hr.example.org/web/index.php');
        $appSetupUtility = $this->getMockBuilder(AppSetupUtility::class)
            ->onlyMethods(['saveInstanceBaseUrl'])
            ->getMock();
        $appSetupUtility->expects($this->once())
            ->method('saveInstanceBaseUrl')
            ->with('https://hr.example.org/web/index.php', true)
            ->willReturn(true);

        $this->callConfigFileStep($appSetupUtility);
    }

    public function testConfigFileStepKeepsStoredUrlWhenFieldLeftEmpty(): void
    {
        StateContainer::getInstance()->storeInstanceBaseUrl(null);
        $appSetupUtility = $this->getMockBuilder(AppSetupUtility::class)
            ->onlyMethods(['saveInstanceBaseUrl'])
            ->getMock();
        // normalize(null) is null, so nothing is written
        $appSetupUtility->expects($this->once())
            ->method('saveInstanceBaseUrl')
            ->with(null, true)
            ->willReturn(false);

        $this->callConfigFileStep($appSetupUtility);
    }

    /**
     * Without a submitted field (e.g. a client that skips the Application URL step) nothing is stored:
     * the URL the installer is opened with is not recorded
     */
    public function testConfigFileStepWithoutFieldStoresNothing(): void
    {
        $appSetupUtility = $this->getMockBuilder(AppSetupUtility::class)
            ->onlyMethods(['saveInstanceBaseUrl'])
            ->getMock();
        $appSetupUtility->expects($this->never())->method('saveInstanceBaseUrl');

        $this->callConfigFileStep($appSetupUtility);
    }

    public function testStoredFlagDefaultsToTrue(): void
    {
        // Completion screens reached without the config-file step (or from the CLI) show no notice
        $this->assertTrue(StateContainer::getInstance()->isInstanceBaseUrlStored());
    }

    public function testConfigFileStepFlagsStoredUrl(): void
    {
        StateContainer::getInstance()->storeInstanceBaseUrl('https://hr.example.org/web/index.php');
        $appSetupUtility = $this->getMockBuilder(AppSetupUtility::class)
            ->onlyMethods(['saveInstanceBaseUrl', 'getStoredInstanceBaseUrl'])
            ->getMock();
        $appSetupUtility->method('saveInstanceBaseUrl')->willReturn(true);
        $appSetupUtility->method('getStoredInstanceBaseUrl')->willReturn('https://hr.example.org/web/index.php');

        $this->assertTrue($this->recordConfigFileStep($appSetupUtility));
        $this->assertTrue(StateContainer::getInstance()->isInstanceBaseUrlStored());
    }

    public function testConfigFileStepFlagsMissingUrl(): void
    {
        // Field left empty and nothing stored
        StateContainer::getInstance()->storeInstanceBaseUrl(null);
        $appSetupUtility = $this->getMockBuilder(AppSetupUtility::class)
            ->onlyMethods(['saveInstanceBaseUrl', 'getStoredInstanceBaseUrl'])
            ->getMock();
        $appSetupUtility->method('saveInstanceBaseUrl')->willReturn(false);
        $appSetupUtility->method('getStoredInstanceBaseUrl')->willReturn(null);

        $this->assertFalse($this->recordConfigFileStep($appSetupUtility));
        $this->assertFalse(StateContainer::getInstance()->isInstanceBaseUrlStored());
    }

    public function testConfigFileStepFlagsFailedStore(): void
    {
        StateContainer::getInstance()->storeInstanceBaseUrl('https://hr.example.org/web/index.php');
        $appSetupUtility = $this->getMockBuilder(AppSetupUtility::class)
            ->onlyMethods(['saveInstanceBaseUrl', 'getStoredInstanceBaseUrl'])
            ->getMock();
        $appSetupUtility->method('saveInstanceBaseUrl')->willThrowException(new RuntimeException('write failed'));
        $appSetupUtility->expects($this->never())->method('getStoredInstanceBaseUrl');
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $logger->expects($this->exactly(2))->method('error');

        $stored = $this->withLogger($logger, fn () => $this->recordConfigFileStep($appSetupUtility));

        $this->assertFalse($stored);
        $this->assertFalse(StateContainer::getInstance()->isInstanceBaseUrlStored());
    }

    /**
     * @dataProvider completeControllerDataProvider
     */
    public function testCompletionScreenGetsStoredFlag(string $controllerClass, ?bool $flag, bool $expected): void
    {
        if ($flag !== null) {
            StateContainer::getInstance()->setInstanceBaseUrlStored($flag);
        }
        $controller = new $controllerClass();
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $this->withLogger($logger, fn () => $controller->preRender($this->request('GET')));

        $props = [];
        foreach ($controller->getComponent()->getProps() as $prop) {
            $props[$prop->getName()] = $prop->getValue();
        }
        $this->assertSame(json_encode($expected), $props['base-url-stored']);
    }

    public function completeControllerDataProvider(): array
    {
        return [
            'upgrader, no URL stored' => [UpgraderCompleteController::class, false, false],
            'upgrader, URL stored' => [UpgraderCompleteController::class, true, true],
            'upgrader, step not run' => [UpgraderCompleteController::class, null, true],
            'installer, no URL stored' => [InstallerCompleteController::class, false, false],
            'installer, URL stored' => [InstallerCompleteController::class, true, true],
        ];
    }

    /**
     * @param string $method
     * @param array $body
     * @return Request
     */
    private function request(string $method, array $body = []): Request
    {
        return Request::create(
            self::UPGRADER_URI,
            $method,
            $body,
            [],
            [],
            [
                'SCRIPT_NAME' => '/orangehrm/installer/index.php',
                'SCRIPT_FILENAME' => '/var/www/orangehrm/installer/index.php',
            ]
        );
    }

    /**
     * Calls handleGet/handlePost directly (handle() refuses to run on an installed system).
     *
     * @param InstanceBaseUrlAPI $api
     * @param Request $request
     * @return array{status: int, body: array}
     */
    private function jsonRequest(string $json): Request
    {
        return Request::create(
            self::UPGRADER_URI,
            'POST',
            [],
            [],
            [],
            [
                'SCRIPT_NAME' => '/orangehrm/installer/index.php',
                'SCRIPT_FILENAME' => '/var/www/orangehrm/installer/index.php',
                'CONTENT_TYPE' => 'application/json',
            ],
            $json
        );
    }

    /**
     * Request to response through the controller (JSON decoding, status, JSON body), as the router would call it,
     * minus the "already installed" guard of handle().
     *
     * @param InstanceBaseUrlAPI $api
     * @param Request $request
     * @return Response
     */
    private function execute(InstanceBaseUrlAPI $api, Request $request): Response
    {
        return $this->invoke($api, 'execute', $request);
    }

    private function call(InstanceBaseUrlAPI $api, Request $request): array
    {
        $body = $this->invoke($api, $request->getMethod() === 'GET' ? 'handleGet' : 'handlePost', $request);
        return ['status' => $this->invoke($api, 'getResponse')->getStatusCode(), 'body' => $body];
    }

    private function callConfigFileStep(AppSetupUtility $appSetupUtility): void
    {
        $this->invoke(new ConfigFileAPI(), 'saveInstanceBaseUrl', $appSetupUtility);
    }

    private function recordConfigFileStep(AppSetupUtility $appSetupUtility): bool
    {
        return $this->invoke(new ConfigFileAPI(), 'recordInstanceBaseUrl', $appSetupUtility);
    }

    /**
     * @param object $logger
     * @param callable $callback
     * @return mixed
     */
    private function withLogger(object $logger, callable $callback)
    {
        $container = ServiceContainer::getContainer();
        $previousLogger = $container->has(Services::LOGGER) ? $container->get(Services::LOGGER) : null;
        $container->set(Services::LOGGER, $logger);
        try {
            return $callback();
        } finally {
            $container->set(Services::LOGGER, $previousLogger);
        }
    }

    /**
     * @param object $object
     * @param string $method
     * @param mixed ...$args
     * @return mixed
     */
    private function invoke(object $object, string $method, ...$args)
    {
        $reflection = new ReflectionMethod($object, $method);
        $reflection->setAccessible(true);
        return $reflection->invoke($object, ...$args);
    }

    private function storeDbInfo(string $dbName): void
    {
        StateContainer::getInstance()->storeDbInfo(
            'mariadb',
            '3306',
            new UserCredential('root', 'root'),
            $dbName
        );
    }

    /**
     * @param callable $configure receives the AppSetupUtility mock
     * @return InstanceBaseUrlAPI
     */
    private function apiWithAppSetupUtility(callable $configure): InstanceBaseUrlAPI
    {
        $appSetupUtility = $this->getMockBuilder(AppSetupUtility::class)
            ->onlyMethods(['getStoredInstanceBaseUrl'])
            ->getMock();
        $configure($appSetupUtility);
        return new class ($appSetupUtility) extends InstanceBaseUrlAPI {
            public function __construct(AppSetupUtility $appSetupUtility)
            {
                $this->appSetupUtility = $appSetupUtility;
            }
        };
    }
}
