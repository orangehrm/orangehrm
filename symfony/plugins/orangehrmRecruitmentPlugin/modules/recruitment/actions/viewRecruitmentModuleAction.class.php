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

class viewRecruitmentModuleAction extends sfAction {
    
    protected $homePageService;
    
    public function getHomePageService() {
        
        if (!$this->homePageService instanceof HomePageService) {
            $this->homePageService = new HomePageService($this->getUser());
        }
        
        return $this->homePageService;
        
    }

    public function setHomePageService($homePageService) {
        $this->homePageService = $homePageService;
    }    

    public function execute($request) {
        $defaultPath = $this->getHomePageService()->getRecruitmentModuleDefaultPath();
        if (empty($defaultPath)) {
            $candidatesPermission = $this->getDataGroupPermissions('recruitment_candidates');
            if ($candidatesPermission->canRead()) {
                $defaultPath = 'recruitment/viewCandidates';
            } else {
                $vacanciesPermission = $this->getDataGroupPermissions('recruitment_vacancies');
                if ($vacanciesPermission->canRead()) {
                    $defaultPath = 'recruitment/viewJobVacancy';
                }
            }
        }

        if (empty($defaultPath)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        } else {
            $this->redirect($defaultPath);
        }
    }
    
    public function getDataGroupPermissions($dataGroups) {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups);
    }    

}
