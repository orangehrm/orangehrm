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

namespace OrangeHRM\Installer\Util;

class Messages
{
    public const MYSQL_CLIENT_RECOMMEND_MESSAGE = 'ver 5.5.x or later recommended';
    public const MYSQL_CLIENT_FAIL_MESSAGE = 'MySQL support not available in PHP settings';

    public const MYSQL_SERVER_FAIL_MESSAGE = 'Not Available';

    public const MAXIMUM_SESSION_IDLE_OK_MESSAGE = 'Good';
    public const MAXIMUM_SESSION_IDLE_SHORT_MESSAGE = 'Short';
    public const MAXIMUM_SESSION_IDLE_TOO_SHORT_MESSAGE = 'Too Short';

    public const REGISTER_GLOBALS_OFF_FAIL_MESSAGE = 'On. Should be off';

    public const STATUS_OK = 'OK';

    public const DEFAULT = 'Default';
    public const AVAILABLE = 'Available';
    public const DISABLED = 'Disabled';
    public const ENABLED = 'Enabled';

    public const WRITEABLE = 'Writeable';
    public const NOT_WRITEABLE = 'Not Writeable';

    public const ERROR_MESSAGE_ACCESS_DENIED = 'Access denied for `Privileged Database User`. ' .
    'Please check `Privileged Database Username` and `Privileged Database User Password` Correct.';

    public const ERROR_MESSAGE_INVALID_HOST_PORT = 'It seems like you are using an incorrect TCP/IP port number or incorrect Unix socket file name. ' .
    'Please check whether MySQL server configuration as well as firewall and port blocking services are enabled.';

    public const ERROR_MESSAGE_REFER_LOG_FOR_MORE = 'For more details, please refer to the error log in src/log/installer.log file';
}
