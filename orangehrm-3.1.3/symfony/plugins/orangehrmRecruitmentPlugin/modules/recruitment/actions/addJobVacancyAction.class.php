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
class addJobVacancyAction extends baseRecruitmentAction {

    private $vacancyService;

    /**
     * Get VacancyService
     * @returns VacncyService
     */
    public function getVacancyService() {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
            $this->vacancyService->setVacancyDao(new VacancyDao());
        }
        return $this->vacancyService;
    }

    /**
     * Set VacancyService
     * @param VacancyService $vacancyService
     */
    public function setVacancyService(VacancyService $vacancyService) {
        $this->vacancyService = $vacancyService;
    }

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function getForm() {
        $this->form->request = $this->getRequest();
        return $this->form;
    }

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {

        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'viewJobVacancy');

        $this->vacancyPermissions = $this->getDataGroupPermissions('recruitment_vacancies');

        $this->vacancyId = $request->getParameter('Id');
        $values = array('vacancyId' => $this->vacancyId, 'vacancyPermissions' => $this->vacancyPermissions);
        $this->setForm(new AddJobVacancyForm(array(), $values));

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
            MessageRegistry::instance()->addMessage($this->message, 'recruitment', 'addJobVacancy', MessageRegistry::PREPEND);
            $this->message = MessageRegistry::instance()->getMessage('recruitment', 'addJobVacancy');
        }

        if ($request->isMethod('post')) {
            if ($this->vacancyPermissions->canCreate() || $this->vacancyPermissions->canUpdate()) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $this->vacancyId = $this->form->save();
                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                    $this->redirect('recruitment/addJobVacancy?Id=' . $this->vacancyId);
                } else {
                    Logger::getLogger('recruitment.addJobVacancy')->error($this->form);
                    $this->getUser()->setFlash('warning', __(TopLevelMessages::SAVE_FAILURE), false);
                }
            }
        }
    }

}

