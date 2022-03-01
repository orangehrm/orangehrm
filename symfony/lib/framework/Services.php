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

    /**
     * @see \Doctrine\ORM\EntityManager
     */
    public const DOCTRINE = 'doctrine.entity_manager';

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
     * @see \OrangeHRM\Core\Service\DateTimeHelperService
     */
    public const DATETIME_HELPER_SERVICE = 'core.datetime_helper_service';

    /**
     * @see \OrangeHRM\Core\Service\TextHelperService
     */
    public const TEXT_HELPER_SERVICE = 'core.text_helper_service';

    /**
     * @see \OrangeHRM\Core\Service\TextHelperService
     */
    public const NUMBER_HELPER_SERVICE = 'core.number_helper_service';

    /**
     * @see \OrangeHRM\Core\Helper\ClassHelper
     */
    public const CLASS_HELPER = 'core.class_helper';

    /**
     * @see \OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager
     */
    public const USER_ROLE_MANAGER = 'core.authorization.user_role_manager';

    /**
     * @see \OrangeHRM\Core\Authorization\Helper\UserRoleManagerHelper
     */
    public const USER_ROLE_MANAGER_HELPER = 'core.authorization.user_role_manager_helper';

    /**
     * @see \OrangeHRM\Framework\Cache\FilesystemAdapter
     * @see \Symfony\Component\Cache\Adapter\AdapterInterface
     */
    public const CACHE = 'core.cache';

    /**
     * @see \OrangeHRM\Core\Service\MenuService
     */
    public const MENU_SERVICE = 'core.menu_service';

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

    /**
     * @see \OrangeHRM\Admin\Service\CompanyStructureService
     */
    public const COMPANY_STRUCTURE_SERVICE = 'admin.company_structure_service';

    /**
     * @see \OrangeHRM\Admin\Service\WorkShiftService
     */
    public const WORK_SHIFT_SERVICE = 'admin.work_shift_service';

    /**
     * @see \OrangeHRM\Admin\Service\LocalizationService
     */
    public const LOCALIZATION_SERVICE = 'admin.localization_service';

    ///////////////////////////////////////////////////////////////
    /// Leave plugin services
    ///////////////////////////////////////////////////////////////

    /**
     * @see \OrangeHRM\Leave\Service\LeaveConfigurationService
     */
    public const LEAVE_CONFIG_SERVICE = 'leave.leave_config_service';

    /**
     * @see \OrangeHRM\Leave\Service\LeaveTypeService
     */
    public const LEAVE_TYPE_SERVICE = 'leave.leave_type_service';

    /**
     * @see \OrangeHRM\Leave\Service\LeaveEntitlementService
     */
    public const LEAVE_ENTITLEMENT_SERVICE = 'leave.leave_entitlement_service';

    /**
     * @see \OrangeHRM\Leave\Service\LeavePeriodService
     */
    public const LEAVE_PERIOD_SERVICE = 'leave.leave_period_service';

    /**
     * @see \OrangeHRM\Leave\Service\LeaveRequestService
     */
    public const LEAVE_REQUEST_SERVICE = 'leave.leave_request_service';

    /**
     * @see \OrangeHRM\Leave\Service\WorkScheduleService
     */
    public const WORK_SCHEDULE_SERVICE = 'leave.work_schedule_service';

    /**
     * @see \OrangeHRM\Leave\Service\HolidayService
     */
    public const HOLIDAY_SERVICE = 'leave.holiday_service';

    /**
     * @see \OrangeHRM\Leave\Service\WorkWeekService
     */
    public const WORK_WEEK_SERVICE = 'leave.work_week_service';

    ///////////////////////////////////////////////////////////////
    /// Pim plugin services
    ///////////////////////////////////////////////////////////////

    /**
     * @see \OrangeHRM\Pim\Service\EmployeeService
     */
    public const EMPLOYEE_SERVICE = 'pim.employee_service';

    ///////////////////////////////////////////////////////////////
    /// Time plugin services
    ///////////////////////////////////////////////////////////////

    /**
     * @see \OrangeHRM\Time\Service\ProjectService
     */
    public const PROJECT_SERVICE = 'time.project_service';

    /**
     * @see \OrangeHRM\Time\Service\CustomerService
     */
    public const CUSTOMER_SERVICE = 'time.customer_service';

    /**
     * @see \OrangeHRM\Time\Service\TimesheetService
     */
    public const TIMESHEET_SERVICE = 'time.timesheet_service';

    ///////////////////////////////////////////////////////////////
    /// Attendance plugin services
    ///////////////////////////////////////////////////////////////

    /**
     * @see \OrangeHRM\Attendance\Service\AttendanceService
     */
    public const ATTENDANCE_SERVICE = 'attendance.attendance_service';

    ///////////////////////////////////////////////////////////////
    /// I18N plugin services
    ///////////////////////////////////////////////////////////////

    /**
     * @see \OrangeHRM\I18N\Service\I18NService
     */
    public const I18N_SERVICE = 'i18n.i18n_service';

    /**
     * @see \OrangeHRM\I18N\Service\I18NHelper
     */
    public const I18N_HELPER = 'i18n.i18n_helper';
}
