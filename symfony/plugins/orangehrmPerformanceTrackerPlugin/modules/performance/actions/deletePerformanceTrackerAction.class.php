<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of removeAction
 *
 * @author indiran
 */
class deletePerformanceTrackerAction extends basePerformanceAction {
    //put your code here

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {

        $form = new DefaultListForm();
        $form->bind($request->getParameter($form->getName()));

        if ($form->isValid()) {
            $toBeDeletedTrackIds = $request->getParameter('chkSelectRow');

            if (!empty($toBeDeletedTrackIds)) {
                foreach ($toBeDeletedTrackIds as $toBeDeletedTrackId) {
                    $this->getPerformanceTrackerService()->DeletePerformanceTracker($toBeDeletedTrackId);
                }
                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
            }
        }
        $this->redirect('performance/addPerformanceTracker');
    }

}

?>
