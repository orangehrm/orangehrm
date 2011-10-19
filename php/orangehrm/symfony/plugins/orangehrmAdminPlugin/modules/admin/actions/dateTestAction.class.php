<?php

class dateTestAction extends sfAction{
    
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

     public function execute($request) {
         $this->setForm(new DateTestForm());
         $dateFormat = new sfDateFormat();
          if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                print_r($this->form->getValue('fromDate'));
                print_r("<br>");
                print_r($this->form->getValue('toDate'));
                
            }
        }
     }
}

