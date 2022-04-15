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
    public const DB_CONFIG_SUCCESS = "Basic configuration successful";
    public const SUPER_USER_NEED = "Need super user credential. (sudo).";
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

    public const WritableLibConfs_OK_MESSAGE = "Write Permissions for lib/confs/ - Writeable";
    public const WritableLibConfs_FAIL_MESSAGE = "Write Permissions for lib/confs/ - Not Writeable";

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

    public const PHPExifEnable_OK_MESSAGE = "PHP exif extension - Enabled";
    public const PHPExifEnable_FAIL_MESSAGE = "PHP exif extension - Not enabled";

    public const PHPAPCEnable_OK_MESSAGE = "PHP APC - Enabled";
    public const PHPAPCEnable_FAIL_MESSAGE = "PHP APC - Not Available. This may affect system performance.";

    public const ApacheExpiresModule_OK_MESSAGE = "Apache expired modules - Enabled";
    public const ApacheExpiresModule_DISABLE_MESSAGE = "Apache expired modules - Disabled. This may affect system performance";
    public const ApacheExpiresModule_UNABLE_MESSAGE = "Apache expired modules - Unable to determine";

    public const ApacheHeadersModule_UNABLE_MESSAGE = "Apache mod_headers module - Unable to determine";
    public const ApacheHeadersModule_ENABLE_MESSAGE = "Apache mod_headers module - Enabled";
    public const ApacheHeadersModule_DISABLE_MESSAGE = "Apache mod_headers module - Disabled. This may affect system performance";

    public const EnableRewriteMod_DISABLE_MESSAGE = "Apache mod_rewrite module - Disabled";
    public const EnableRewriteMod_OK_MESSAGE = "Apache mod_rewrite module - Enabled";
    public const EnableRewriteMod_UNABLE_MESSAGE = "Apache mod_rewrite module - Unable to determine";

    public const MySQLEventStatus_FAIL_MESSAGE = "MySQL Event Scheduler status - Cannot connect to the database";
    public const MySQLEventStatus_DISABLE_MESSAGE = "MySQL Event Scheduler status - Disabled. This is required for automatic leave status changes of Leave module";
    public const MySQLEventStatus_OK_MESSAGE = "MySQL Event Scheduler status - Enabled";

    public const CURLStatus_DISABLE_MESSAGE = "cURL status - Disabled. This is required to run OrangeHRM";
    public const CURLStatus_OK_MESSAGE = "cURL status - Enabled";

    public const SimpleXMLStatus_DISABLE_MESSAGE = "SimpleXML status - Disabled. SimpleXML, libxml and xml PHP libraries are required";
    public const SimpleXMLStatus_OK_MESSAGE = "SimpleXML status - Enabled";

    public const ZIP_Status_DISABLE_MESSAGE = "ZIP status - Disabled. SimpleXML, libxml and xml PHP libraries are required";
    public const ZIP_Status_OK_MESSAGE = "ZIP status - Enabled";

    public const INTERUPT_MESSAGE = "Above error found. Please correct it to continue";
    public const DB_WRONG_INFO = "Please check Database name, port , Privileged user name and password";

    public const PHP_MIN_VERSION = '5.3.0';
    public const MYSQL_MIN_VERSION = '4.1.0';

    public const MYSQL_ERR_DEFAULT_MESSAGE = "Unable to connect to MySQL server. Please check MySQL server is running and given database information are correct.";
    public const MYSQL_ERR_DB_NOT_EMPTY = "Database `%s` is not empty. Please use another empty database or cleanup the given database.";
    public const MYSQL_ERR_DATABASE_EXIST = "Database `%s` already exists. Please use another database name.";
    public const MYSQL_ERR_DB_NOT_EXIST = "Database `%s` not exists. Please check database name and try again.";
    public const MYSQL_ERR_DB_USER_EXIST = "Database User `%s` already exists. Please use another username for 'OrangeHRM Database User'.";
    public const MYSQL_ERR_DB_ACCESS_DENIED = "Access denied for user `%s` to database `%s`. Please give all privileges to the user for the particular database.";
    public const MYSQL_ERR_ACCESS_DENIED = "Access denied for 'Privileged Database User'. Please check 'Privileged Database Username' and 'Privileged Database User Password' Correct.";
    public const MYSQL_ERR_MESSAGE = "\n\nMySQL Error Code: %s\nMessage: %s";
    public const MYSQL_ERR_CONN_ERROR = "The MySQL server isn't running on `%s:%s`. It seems like you are using an incorrect TCP/IP port number or incorrect Unix socket file name. Please check whether MySQL server configuration as well as firewall and port blocking services are enabled.";
    public const MYSQL_ERR_CONN_HOST_ERROR = "Can't connect to MySQL server on `%s`. It seems like the network connection has been refused. Please check whether your MySQL server is running and has an active network connection. Also, check your specified port configured on the server.";
    public const MYSQL_ERR_UNKNOWN_PROTOCOL = "Database connection is trying to go through a wrong protocol. Please use TCP/IP protocol.";
    public const MYSQL_ERR_CLEANUP_CONN_FAILED = "Cleanup installation is failing due to database connection error.";
    public const MYSQL_ERR_CANT_CREATE_DB = "Unable to create database.";
    public const MYSQL_ERR_CANT_CONNECT_TO_DB = "Can't connect to `%s` database.";
    public const MYSQL_ERR_EXTENSION_NOT_ENABLED = "Please enable %s in PHP modules to continue with the installation.";
}
