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
    public const MYSQL_CLIENT_RECOMMEND_MESSAGE = "MySQL Client - ver 5.5.x or later recommended";
    public const MYSQL_CLIENT_FAIL_MESSAGE = "MySQL Client - MySQL support not available in PHP settings";
    public const MYSQL_SERVER_FAIL_MESSAGE = "MySQL Server - Not Available";
    public const OK_MESSAGE = "OK";

    public const WRITEABLE = "Writeable";
    public const NON_WRITEABLE = "Not Writeable";

    public const MAXIMUM_SESSION_IDLE_SHORT_MESSAGE = "Maximum Session Idle Time before Timeout - Short";
    public const MAXIMUM_SESSION_IDLE_TOO_SHORT_MESSAGE = "Maximum Session Idle Time before Timeout - Too Short";

    public const REGISTER_GLOBALS_OFF_FAIL_MESSAGE = "On. Should be off";
    public const CURL_STATUS_DISABLE_MESSAGE = "cURL status - Disabled. This is required to run OrangeHRM";
    public const SIMPLE_XML_STATUS_DISABLE_MESSAGE = "SimpleXML status - Disabled. SimpleXML, libxml and xml PHP libraries are required";
    public const ZIP_STATUS_DISABLE_MESSAGE = "ZIP status - Disabled. SimpleXML, libxml and xml PHP libraries are required";
}
