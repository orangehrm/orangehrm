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
     * @see \OrangeHRM\Framework\Http\RequestStack
     */
    public const REQUEST_STACK = 'request_stack';

    /**
     * @see \OrangeHRM\Framework\Routing\RequestContext
     */
    public const ROUTER_REQUEST_CONTEXT = 'router.request_context';

    /**
     * @see \OrangeHRM\Framework\Routing\UrlMatcher
     */
    public const ROUTER = 'router.default';

    /**
     * @see \OrangeHRM\Framework\Event\EventDispatcher
     */
    public const EVENT_DISPATCHER = 'event_dispatcher';

    /**
     * @see \OrangeHRM\Framework\Http\ControllerResolver
     */
    public const CONTROLLER_RESOLVER = 'controller_resolver';

    /**
     * @see \Symfony\Component\HttpKernel\Controller\ArgumentResolver
     */
    public const ARGUMENT_RESOLVER = 'argument_resolver';

    /**
     * @see \OrangeHRM\Framework\Framework
     */
    public const HTTP_KERNEL = 'http_kernel';

    /**
     * @see \OrangeHRM\Framework\Http\Session\NativeSessionStorage
     */
    public const SESSION_STORAGE = 'session_storage';

    /**
     * @see \OrangeHRM\Framework\Http\Session\Session
     */
    public const SESSION = 'session';

    /**
     * @see \OrangeHRM\Framework\Logger\Logger
     */
    public const LOGGER = 'logger';

    /**
     * @see \OrangeHRM\Framework\Routing\UrlGenerator
     */
    public const URL_GENERATOR = 'url_generator';

    /**
     * @see \Symfony\Component\HttpFoundation\UrlHelper
     */
    public const URL_HELPER = 'url_helper';

    ///////////////////////////////////////////////////////////////
    /// Core plugin services
    ///////////////////////////////////////////////////////////////

    /**
     * @see \OrangeHRM\Core\Service\ConfigService
     */
    public const CONFIG_SERVICE = 'core.config_service';

    /**
     * @see \OrangeHRM\Core\Service\NormalizerService
     */
    public const NORMALIZER_SERVICE = 'core.normalizer_service';

    /**
     * @see \OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager
     */
    public const USER_ROLE_MANAGER = 'core.authorization.user_role_manager';

    ///////////////////////////////////////////////////////////////
    /// Authentication plugin services
    ///////////////////////////////////////////////////////////////

    /**
     * @see \OrangeHRM\Authentication\Auth\User
     */
    public const AUTH_USER = 'auth.user';

    ///////////////////////////////////////////////////////////////
    /// Admin plugin services
    ///////////////////////////////////////////////////////////////

    /**
     * @see \OrangeHRM\Admin\Service\CountryService
     */
    public const COUNTRY_SERVICE = 'admin.country_service';

    /**
     * @see \OrangeHRM\Admin\Service\PayGradeService
     */
    public const PAY_GRADE_SERVICE = 'admin.pay_grade_service';

    /**
     * @see \OrangeHRM\Admin\Service\UserService
     */
    public const USER_SERVICE = 'admin.user_service';

    ///////////////////////////////////////////////////////////////
    /// Pim plugin services
    ///////////////////////////////////////////////////////////////

    /**
     * @see \OrangeHRM\Pim\Service\EmployeeService
     */
    public const EMPLOYEE_SERVICE = 'pim.employee_service';
}
