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

namespace OrangeHRM\Installer\Framework;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Event\EventDispatcher;
use OrangeHRM\Framework\Filesystem\Filesystem;
use OrangeHRM\Framework\Http\ControllerResolver;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\RequestStack;
use OrangeHRM\Framework\Http\Session\NativeSessionStorage;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Logger\Logger;
use OrangeHRM\Framework\Routing\RequestContext;
use OrangeHRM\Framework\Routing\UrlMatcher;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\Installer\Exception\SessionStorageNotWritable;
use OrangeHRM\Installer\Subscriber\ExceptionSubscriber;
use OrangeHRM\Installer\Subscriber\LoggerSubscriber;
use OrangeHRM\Installer\Util\SystemCheck;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel as BaseHttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class HttpKernel extends BaseHttpKernel
{
    /**
     * @var null|string
     */
    protected ?string $environment = null;
    /**
     * @var null|bool
     */
    protected ?bool $debug = null;
    /**
     * @var string
     */
    private string $logFilePath;
    /**
     * @var bool
     */
    private ?bool $isLogFileWritable = null;

    public function __construct(string $environment, bool $debug)
    {
        $this->environment = $environment;
        $this->debug = $debug;
        $this->configureContainer();
        $this->configureLogger();

        /** @var EventDispatcher $dispatcher */
        $dispatcher = ServiceContainer::getContainer()->get(Services::EVENT_DISPATCHER);
        /** @var ControllerResolver $resolver */
        $resolver = ServiceContainer::getContainer()->get(Services::CONTROLLER_RESOLVER);
        /** @var RequestStack $requestStack */
        $requestStack = ServiceContainer::getContainer()->get(Services::REQUEST_STACK);
        /** @var ArgumentResolver $argumentResolver */
        $argumentResolver = ServiceContainer::getContainer()->get(Services::ARGUMENT_RESOLVER);

        parent::__construct($dispatcher, $resolver, $requestStack, $argumentResolver);
    }

    protected function configureContainer(): void
    {
        ServiceContainer::getContainer()->register(Services::REQUEST_STACK, RequestStack::class);
        ServiceContainer::getContainer()->register(Services::ROUTER_REQUEST_CONTEXT, RequestContext::class);
        ServiceContainer::getContainer()->register(Services::EVENT_DISPATCHER, EventDispatcher::class);
        ServiceContainer::getContainer()->register(Services::CONTROLLER_RESOLVER, ControllerResolver::class);
        ServiceContainer::getContainer()->register(Services::ARGUMENT_RESOLVER, ArgumentResolver::class);
        ServiceContainer::getContainer()->set(Services::HTTP_KERNEL, $this);
    }

    protected function configureLogger(): void
    {
        $logger = new Logger('installer');
        $this->logFilePath = Config::get(Config::LOG_DIR) . DIRECTORY_SEPARATOR . 'installer.log';
        $logLevel = $this->isDebug()
            ? Logger::DEBUG
            : ($this->isLogFileWritable() ? Logger::INFO : Logger::WARNING);

        $logger->pushHandler(new StreamHandler($this->logFilePath, $logLevel));
        ServiceContainer::getContainer()->set(Services::LOGGER, $logger);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = ServiceContainer::getContainer()->get(Services::EVENT_DISPATCHER);
        $dispatcher->addSubscriber(new LoggerSubscriber());
    }

    /**
     * @return bool
     */
    public function isLogFileWritable(): bool
    {
        if (is_null($this->isLogFileWritable)) {
            try {
                $filesystem = new Filesystem();
                $filesystem->appendToFile($this->logFilePath, '');
                $this->isLogFileWritable = true;
            } catch (IOException $e) {
                $this->isLogFileWritable = false;
            }
        }
        return $this->isLogFileWritable;
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    protected function configureRouter(Request $request): void
    {
        /** @var RequestContext $context */
        $context = ServiceContainer::getContainer()->get(Services::ROUTER_REQUEST_CONTEXT);
        $context->fromRequest($request);

        $routes = $this->getRoutes();
        ServiceContainer::getContainer()->register(Services::ROUTER, UrlMatcher::class)
            ->addArgument($routes)
            ->addArgument($context);

        /** @var UrlMatcher $matcher */
        $matcher = ServiceContainer::getContainer()->get(Services::ROUTER);
        /** @var RequestStack $requestStack */
        $requestStack = ServiceContainer::getContainer()->get(Services::REQUEST_STACK);

        /** @var Logger $logger */
        $logger = ServiceContainer::getContainer()->get(Services::LOGGER);

        $routerListener = new RouterListener($matcher, $requestStack, $context, $logger, null, $this->isDebug());
        /** @var EventDispatcher $dispatcher */
        $dispatcher = ServiceContainer::getContainer()->get(Services::EVENT_DISPATCHER);
        $dispatcher->addListener(KernelEvents::REQUEST, [$routerListener, 'onKernelRequest'], 99500);
        $dispatcher->addListener(KernelEvents::FINISH_REQUEST, [$routerListener, 'onKernelFinishRequest']);
        $dispatcher->addListener(KernelEvents::EXCEPTION, [$routerListener, 'onKernelException'], -64);
    }

    /**
     * @return RouteCollection
     */
    protected function getRoutes(): RouteCollection
    {
        $locator = new FileLocator();
        $yamlFileLoader = new YamlFileLoader($locator);
        $routes = new RouteCollection();

        $routePath = realpath(__DIR__ . '/../config/routes.yaml');
        if ($routePath) {
            $pluginRoutes = $yamlFileLoader->load($routePath);
            $routes->addCollection($pluginRoutes);
        }
        return $routes;
    }

    /**
     * @param Request $request
     * @throws SessionStorageNotWritable
     */
    protected function configureSession(Request $request): void
    {
        $systemCheck = new SystemCheck();
        $savePath = Config::get(Config::SESSION_DIR);
        if ($savePath != null && $systemCheck->checkWritePermission(Config::get(Config::SESSION_DIR))) {
            $savePath = Config::get(Config::SESSION_DIR);
        } elseif ($systemCheck->checkWritePermission(sys_get_temp_dir())) {
            $savePath = sys_get_temp_dir();
        } else {
            throw new SessionStorageNotWritable('Session storage not writable.');
        }
        $session = $this->createSession($request, $savePath);
        $session->start();
        ServiceContainer::getContainer()->set(Services::SESSION, $session);
    }

    /**
     * @param Request $request
     * @param string|null $savePath
     * @return Session
     */
    protected function createSession(Request $request, string $savePath = null): Session
    {
        $isSecure = $request->isSecure();
        $path = $request->getBasePath();
        $options = [
            'name' => $isSecure ? 'orangehrm' : '_orangehrm',
            'cookie_secure' => $isSecure,
            'cookie_httponly' => true,
            'cookie_path' => $path,
            'cookie_samesite' => 'Strict',
        ];
        $sessionStorage = new NativeSessionStorage($options, new NativeFileSessionHandler($savePath));
        ServiceContainer::getContainer()->set(Services::SESSION_STORAGE, $sessionStorage);
        return new Session($sessionStorage);
    }

    protected function configureSubscribers(): void
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = ServiceContainer::getContainer()->get(Services::EVENT_DISPATCHER);
        $dispatcher->addSubscriber(new ExceptionSubscriber());
    }

    /**
     * @param Request $request
     * @param int $type
     * @param bool $catch
     * @return Response
     */
    public function handleRequest(
        Request $request,
        int $type = HttpKernelInterface::MAIN_REQUEST,
        bool $catch = true
    ): Response {
        $this->configureSubscribers();
        $this->configureRouter($request);
        $this->configureSession($request);

        return $this->handle($request, $type, $catch);
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }
}
