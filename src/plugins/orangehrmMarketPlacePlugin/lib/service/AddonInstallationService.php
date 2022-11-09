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

/**
 * This interface must be implemented by all OS Add-ons that are published
 * to the market place.
 * @author rimaz
 */
interface AddonInstallationService {

    /**
     * This method will run all necessary scripts and commands to install the
     * add-on and return an array with the relevant status and message.
     * @return array Array will consist of two keys : the status & message.
     * @example array("status" => true, "message" => "Successfully Installed.");
     */
    public function install();

    /**
     * This method will run all necessary scripts and commands to uninstall the
     * add-on and return an array with the relevant status and message.
     * @return array Array will consist of two keys : the status & message.
     * @example array("status" => true, "message" => "Successfully Uninstalled.");
     */
    public function uninstall();
}
