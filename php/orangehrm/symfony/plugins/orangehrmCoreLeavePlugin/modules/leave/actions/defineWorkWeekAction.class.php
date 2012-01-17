<?php

class defineWorkWeekAction extends baseLeaveAction {

    public function execute($request) {

        $operationalCountryId = $request->getParameter('operationalCountryId', null);
        $workWeek = $this->getWorkWeekService()->getWorkWeekOfOperationalCountry($operationalCountryId);

        $this->workWeekForm = new WorkWeekForm(array('workWeekEntity' => $workWeek));        

        /* authentication */
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 'Yes') {
            $this->forward('leave', 'viewMyLeaveList');
        }

        if ($request->isMethod(sfRequest::POST)) {
            $workWeekService = $this->getWorkWeekService();
            $this->workWeekForm->bind($request->getParameter($this->workWeekForm->getName()));

            if ($this->workWeekForm->isValid()) {
                try {
                    
                    $workWeek->setMon($this->workWeekForm->getValue('day_length_Monday'));
                    $workWeek->setTue($this->workWeekForm->getValue('day_length_Tuesday'));
                    $workWeek->setWed($this->workWeekForm->getValue('day_length_Wednesday'));
                    $workWeek->setThu($this->workWeekForm->getValue('day_length_Thursday'));
                    $workWeek->setFri($this->workWeekForm->getValue('day_length_Friday'));
                    $workWeek->setSat($this->workWeekForm->getValue('day_length_Saturday'));
                    $workWeek->setSun($this->workWeekForm->getValue('day_length_Sunday'));
                    
                    $this->getWorkWeekService()->saveWorkWeek($workWeek);
                    $this->templateMessage = array('SUCCESS', __('Work Week Successfully Saved'));
                } catch (Exception $e) {
                    $this->templateMessage = array('FAILURE', __('Failed to Save Work Week'));
                }
            }
        }
    }

}
