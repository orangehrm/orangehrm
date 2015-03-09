<?php

/*
 *
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
 *
 */

/**
 * Helper class to include files required by different versions of PHPUnit.
 * 
 */
class PHPUnitVersionHelper {

    public static function includeRequiredFiles() {

        // Check PHPUnit Version.
        if (stream_resolve_include_path("PHPUnit/Runner/Version.php")) {
            require_once 'PHPUnit/Runner/Version.php';
            $phpunitVersion = PHPUnit_Runner_Version::id();

            if (version_compare($phpunitVersion, '3.5.0') < 0) {
                echo('Your version of PHPUnit is outdated. Detected version: ' . $phpunitVersion . ". Please update to 3.5 or newer.\n");
            }

            // PHPUnit >= 3.5 no longer requires Framework.php
            if (version_compare($phpunitVersion, '3.5.0') >= 0) {
                require_once 'PHPUnit/Autoload.php';
            } else {
                require_once 'PHPUnit/Framework.php';
            }
        }
    }

}

?>
