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

namespace OrangeHRM\Installer\Util;

class Messages
{
    public const SEPERATOR = "----------------------------------";

    public const PHP_OK_MESSAGE = "PHP Version - Ok";
    public const PHP_FAIL_MESSAGE = "PHP Version - PHP 5.3.0 or higher is required";

    public const MYSQL_CLIENT_OK_MESSAGE = "MySQL Client - Ok";
    public const MYSQL_CLIENT_RECOMMEND_MESSAGE = "MySQL Client - ver 4.1.x or later recommended";
    public const MYSQL_CLIENT_FAIL_MESSAGE = "MySQL Client - MySQL support not available in PHP settings";

    public const MYSQL_SERVER_OK_MESSAGE = "MySQL Server - Ok";
    public const MYSQL_SERVER_RECOMMEND_MESSAGE = "MySQL Server - ver 5.1.6 or later recommended";
    public const MYSQL_SERVER_FAIL_MESSAGE = "MySQL Server - Not Available";

    public const WEB_SERVER_OK_MESSAGE = "OK";

    public const WRITABLE_LIB_CONFS_OK_MESSAGE = "Write Permissions for lib/confs/ - Writeable";
    public const WRITABLE_LIB_CONFS_FAIL_MESSAGE = "Write Permissions for lib/confs/ - Not Writeable";

    public const WritableSymfonyConfig_OK_MESSAGE = "Write Permissions for - symfony/config - Ok";
    public const WritableSymfonyConfig_FAIL_MESSAGE = "Write Permissions for - symfony/config - not writeable";

    public const WritableSymfonyCache_OK_MESSAGE = "Write Permissions for symfony/cache - Writeable";
    public const WritableSymfonyCache_FAIL_MESSAGE = "Write Permissions for symfony/cache - Not Writeable";

    public const WritableSymfonyLog_OK_MESSAGE = "Write Permissions for symfony/log - Writeable";
    public const WritableSymfonyLog_FAIL_MESSAGE = "Write Permissions for symfony/log - Not Writeable";

    public const MaximumSessionIdle_OK_MESSAGE = "Maximum Session Idle Time before Timeout - Good";
    public const MaximumSessionIdle_SHORT_MESSAGE = "Maximum Session Idle Time before Timeout - Short";
    public const MaximumSessionIdle_TOO_SHORT_MESSAGE = "Maximum Session Idle Time before Timeout - Too short";

    public const RegisterGlobalsOff_OK_MESSAGE = "Ok";
    public const RegisterGlobalsOff_FAIL_MESSAGE = "On. Should be off";

    public const GgExtensionEnable_OK_MESSAGE = "PHP gd extension - Enabled";
    public const GgExtensionEnable_FAIL_MESSAGE = "PHP gd extension - Not enabled";

    public const MySQLEventStatus_FAIL_MESSAGE = "MySQL Event Scheduler status - Cannot connect to the database";
    public const MySQLEventStatus_DISABLE_MESSAGE = "MySQL Event Scheduler status - Disabled. This is required for automatic leave status changes of Leave module";
    public const MySQLEventStatus_OK_MESSAGE = "MySQL Event Scheduler status - Enabled";

    public const CURLStatus_DISABLE_MESSAGE = "cURL status - Disabled. This is required to run OrangeHRM";
    public const CURLStatus_OK_MESSAGE = "cURL status - Enabled";

    public const SimpleXMLStatus_DISABLE_MESSAGE = "SimpleXML status - Disabled. SimpleXML, libxml and xml PHP libraries are required";
    public const SimpleXMLStatus_OK_MESSAGE = "SimpleXML status - Enabled";

    public const ZIP_Status_DISABLE_MESSAGE = "ZIP status - Disabled. SimpleXML, libxml and xml PHP libraries are required";
    public const ZIP_Status_OK_MESSAGE = "ZIP status - Enabled";

    public const PHP_MIN_VERSION = '5.3.0';
    public const MYSQL_MIN_VERSION = '4.1.0';
}
