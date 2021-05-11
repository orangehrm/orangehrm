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

final class Services
{
    /**
     * \OrangeHRM\Framework\Http\RequestStack
     */
    public const REQUEST_STACK = 'request_stack';

    /**
     * \OrangeHRM\Framework\Routing\RequestContext
     */
    public const ROUTER_REQUEST_CONTEXT = 'router.request_context';

    /**
     * \OrangeHRM\Framework\Routing\UrlMatcher
     */
    public const ROUTER = 'router.default';

    /**
     * \OrangeHRM\Framework\Event\EventDispatcher
     */
    public const EVENT_DISPATCHER = 'event_dispatcher';

    /**
     * \OrangeHRM\Framework\Http\ControllerResolver
     */
    public const CONTROLLER_RESOLVER = 'controller_resolver';

    /**
     * \Symfony\Component\HttpKernel\Controller\ArgumentResolver
     */
    public const ARGUMENT_RESOLVER = 'argument_resolver';

    /**
     * \OrangeHRM\Framework\Framework
     */
    public const HTTP_KERNEL = 'http_kernel';

    /**
     * \OrangeHRM\Framework\Http\Session\NativeSessionStorage
     */
    public const SESSION_STORAGE = 'session_storage';

    /**
     * \OrangeHRM\Framework\Http\Session\Session
     */
    public const SESSION = 'session';

    /**
     * \OrangeHRM\Framework\Logger\Logger
     */
    public const LOGGER = 'logger';

    /**
     * \OrangeHRM\Framework\Routing\UrlGenerator
     */
    public const URL_GENERATOR = 'url_generator';

    /**
     * Symfony\Component\HttpFoundation\UrlHelper
     */
    public const URL_HELPER = 'url_helper';
}
