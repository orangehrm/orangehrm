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
 *
 */
class applyVacancyAction extends sfAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    /**
     *
     * @return ApplyVacancyForm 
     */
    public function getForm() {
        return $this->form;
    }

    /**
     *
     * @return <type>
     */
    public function getVacancyService() {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
            $this->vacancyService->setVacancyDao(new VacancyDao());
        }
        return $this->vacancyService;
    }

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {
        $param = null;
        $this->candidateId = null;

        $this->vacancyId = $request->getParameter('id');
        //$this->candidateId = $request->getParameter('candidateId');
        $this->getResponse()->setTitle(__("Vacancy Apply Form"));
        //$param = array('candidateId' => $this->candidateId);
        $this->setForm(new ApplyVacancyForm(array(), $param, true));

        if (!empty($this->vacancyId)) {
            $vacancy = $this->getVacancyService()->getVacancyById($this->vacancyId);
            if (empty($vacancy)) {
                $this->redirect('recruitmentApply/jobs.html');
            }
            $this->description = $vacancy->getDescription();
            $this->name = $vacancy->getName();
        } else {
            $this->redirect('recruitmentApply/jobs.html');
        }
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
            $file = $request->getFiles($this->form->getName());

            if ($_FILES['addCandidate']['size']['resume'] > 1024000) {
                $this->getUser()->setFlash('applyVacancy.warning', __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE));
            } else if ($_FILES == null) {
                $this->getUser()->setFlash('applyVacancy.warning', __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE));
                $this->redirect('recruitmentApply/applyVacancy?id=' . $this->vacancyId);
            } else {

                if ($this->form->isValid()) {

                    $result = $this->form->save();
                    if (isset($result['messageType'])) {
                        $this->getUser()->setFlash('applyVacancy.' . $result['messageType'], $result['message']);
                    } else {
                        $this->candidateId = $result['candidateId'];
                        if (!empty($this->candidateId)) {
                            $this->getUser()->setFlash('applyVacancy.success', __('Application Received'));
                            $this->getUser()->setFlash('applyVacancy.warning', null);
                        }
                    }
                }
            }
        }
    }

}

