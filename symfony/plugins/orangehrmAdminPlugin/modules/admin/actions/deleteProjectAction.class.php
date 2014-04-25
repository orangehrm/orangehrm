<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleteProjectAction
 *
 * @author orangehrm
 */
class deleteProjectAction extends baseAdminAction {

    private $projectService;

    public function getProjectService() {
        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
            $this->projectService->setProjectDao(new ProjectDao());
        }
        return $this->projectService;
    }

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {

        $toBeDeletedProjectIds = $request->getParameter('chkSelectRow');

        $projectPermissions = $this->getDataGroupPermissions('time_projects');

        if ($projectPermissions->canDelete()) {
            $form = new DefaultListForm();
            $form->bind($request->getParameter($form->getName()));
            if ($form->isValid()) {
                if (!empty($toBeDeletedProjectIds)) {
                    $delete = true;
                    foreach ($toBeDeletedProjectIds as $toBeDeletedProjectId) {
                        $deletable = $this->getProjectService()->hasProjectGotTimesheetItems($toBeDeletedProjectId);
                        if ($deletable) {
                            $delete = false;
                            break;
                        }
                    }
                    if ($delete) {
                        foreach ($toBeDeletedProjectIds as $toBeDeletedProjectId) {

                            $customer = $this->getProjectService()->deleteProject($toBeDeletedProjectId);
                        }
                        $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                    } else {
                        $this->getUser()->setFlash('error', __('Not Allowed to Delete Project(s) Which Have Time Logged Against Them'));
                    }
                }
            }

            $this->redirect('admin/viewProjects');
        }
    }

}

?>
