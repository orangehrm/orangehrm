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

class OrangeI18NFilter extends sfFilter {

    public function execute($filterChain) {


        //
        // Get culture and datepattern here:
        //
        // Eg: from settings file or database config table.
        //
        // If no date pattern, we can get the date pattern from
        // the user culture:
        //
        // $dateFormat = new sfDateFormat($userCulture);
        //
        // $datePattern = $dateFormat->getPattern('d'); // short date pattern
        // $timePattern = $dateFormat->getPattern('t'); // short time pattern
        //
        // (also: $culture->DateTimeFormat->getShortDatePattern();)
        //

        $userCulture = $this->getContext()->getUser()->getCulture();
        $datePattern = 'dd-MM-yyyy';

        $user = $this->getContext()->getUser();
        $user->setCulture($userCulture);
        $user->setDateFormat($datePattern);


        // Execute next filter in filter chain
        $filterChain->execute();
    }
}