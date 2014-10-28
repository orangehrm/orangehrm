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

class ModuleFilter extends sfFilter {

    public function execute($filterChain) {

        /* Populating enabled modules */
        
        $disabledModules = array();
        
        if ($this->getContext()->getUser()->hasAttribute("admin.disabledModules")) {
            
            $disabledModules = $this->getContext()->getUser()->getAttribute("admin.disabledModules");
            
        } else {
            
            $moduleService = new ModuleService();
            $disabledModuleList = $moduleService->getDisabledModuleList();
            
            foreach ($disabledModuleList as $module) {
                $disabledModules[] = $module->getName();
            }
            
            $this->getContext()->getUser()->setAttribute("admin.disabledModules", $disabledModules);
            
        }
        
        /* Checking request with disabled modules */
        
        $request = $this->getContext()->getRequest();
        
        if (in_array($request['module'], $disabledModules)) {
            header("HTTP/1.0 404 Not Found");
            die;
        }
        
        /* Continuing the filter chain */

        $filterChain->execute();
        
    }

}
