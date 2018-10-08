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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class purgeCandidateDataAction
 */
class purgeCandidateDataAction extends sfAction
{
    private $systemUserService = null;
    private $vacancyService = null;
    private $maintenanceManager = null;

    /**
     * @param sfRequest $request
     * @return mixed|void
     * @throws sfException
     */

    public function execute($request)
    {
        $this->getUser()->setFlash('warning', null);
        $this->getUser()->setFlash('success', null);

        $data = $request->getParameterHolder()->getAll();
        $checkIfReqestToAuthenticate = $request->hasParameter('check_authenticate');
        $requestmethod = $request->getMethod();

        if ($requestmethod == 'GET') {
            $this->purgeAuthenticateForm = new PurgeAuthenticateForm();
            $this->setTemplate('purgeEmployee', 'maintenance');
        } elseif ($requestmethod == 'POST' and $checkIfReqestToAuthenticate) {
            $userId = sfContext::getInstance()->getUser()->getAttribute('auth.userId');
            if ($this->getSystemUserService()->isCurrentPassword($userId, $data['confirm_password'])) {
                $this->getUser()->setFlash('success', __(CommonMessages::CREDENTIALS_VALID));
                $this->purgeCandidateForm = new PurgeCandidateForm();
            } else {
                $this->purgeAuthenticateForm = new PurgeAuthenticateForm();
                $this->setTemplate('purgeEmployee', 'maintenance');
                $this->getUser()->setFlash('warning', __(CommonMessages::CREDENTIALS_REQUIRED));
            }
        } elseif ($requestmethod == 'POST' and !$checkIfReqestToAuthenticate) {
            $this->purgeCandidate($data);
            $this->purgeCandidateForm = new PurgeCandidateForm();
        }
    }

    /**
     * @return null|SystemUserService
     */
    protected function getSystemUserService()
    {
        if (!isset($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }

    /**
     * @return mixed|VacancyService
     */
    public function getVacancyService()
    {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
        }
        return $this->vacancyService;
    }

    /**
     * @return MaintenanceManager|null
     */
    public function getMaintenanceManager()
    {
        if (!isset($this->maintenanceManager)) {
            $this->maintenanceManager = new MaintenanceManager();
        }
        return $this->maintenanceManager;
    }

    /**
     * @param $Candidatadata
     */
    protected function purgeCandidate($vacancyData)
    {
        try {
            $vacancyNumber = $vacancyData['candidate']['empId'];
            $vacancy = $this->getVacancyService()->getVacancyById($vacancyNumber);
            if (empty($vacancy)) {
                $this->getUser()->setFlash('warning', __(ValidationMessages::VACANCY_DOES_NOT_EXIST));
            } else {
                $this->getMaintenanceManager()->purgeCandidateData($vacancyNumber);
                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
//             @codeCoverageIgnoreStart
            }
        } catch (Exception $e) {
            $this->getUser()->setFlash('warning', __(TopLevelMessages::PURGE_CANDIDATE_FAILED));
            $this->purgeCandidateForm = new PurgeCandidateForm();
        }
//            @codeCoverageIgnoreEnd
    }
}
