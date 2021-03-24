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
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Framework;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Framework extends HttpKernel
{
    /**
     * @var null|string
     */
    protected $environment = null;
    /**
     * @var null|bool
     */
    protected $debug = null;

    public function __construct(string $environment, bool $debug)
    {
        $this->environment = $environment;
        $this->debug = $debug;
        $this->configureContainer();

        /** @var EventDispatcher $dispatcher */
        $dispatcher = ServiceContainer::getContainer()->get(Services::EVENT_DISPATCHER);
        /** @var ControllerResolver $resolver */
        $resolver = ServiceContainer::getContainer()->get(Services::CONTROLLER_RESOLVER);
        /** @var RequestStack $requestStack */
        $requestStack = ServiceContainer::getContainer()->get(Services::REQUEST_STACK);
        /** @var ArgumentResolver $argumentResolver */
        $argumentResolver = ServiceContainer::getContainer()->get(Services::ARGUMENT_RESOLVER);

        ServiceContainer::getContainer()->set(Services::HTTP_KERNEL, $this);

        parent::__construct($dispatcher, $resolver, $requestStack, $argumentResolver);
    }

    protected function configureContainer(): void
    {
        ServiceContainer::getContainer()->register(Services::REQUEST_STACK, RequestStack::class);
        ServiceContainer::getContainer()->register(Services::ROUTER_REQUEST_CONTEXT, RequestContext::class);
        ServiceContainer::getContainer()->register(Services::EVENT_DISPATCHER, EventDispatcher::class);
        ServiceContainer::getContainer()->register(Services::CONTROLLER_RESOLVER, ControllerResolver::class);
        ServiceContainer::getContainer()->register(Services::ARGUMENT_RESOLVER, ArgumentResolver::class);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        /** @var RequestContext $context */
        $context = ServiceContainer::getContainer()->get(Services::ROUTER_REQUEST_CONTEXT);
        $context->fromRequest($request);

        ServiceContainer::getContainer()->register(Services::ROUTER, UrlMatcher::class)
            ->addArgument(RouteManager::getRoutes())
            ->addArgument($context);

        /** @var UrlMatcher $matcher */
        $matcher = ServiceContainer::getContainer()->get(Services::ROUTER);
        /** @var RequestStack $requestStack */
        $requestStack = ServiceContainer::getContainer()->get(Services::REQUEST_STACK);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = ServiceContainer::getContainer()->get(Services::EVENT_DISPATCHER);
        $dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));

        return parent::handle($request, $type, $catch);
    }
}
