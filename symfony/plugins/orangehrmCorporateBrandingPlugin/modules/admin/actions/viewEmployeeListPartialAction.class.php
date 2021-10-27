<?php

class viewEmployeeListPartialAction extends viewEmployeeListAction
{
    public function preExecute()
    {
    }

    public function execute($request)
    {
        parent::execute($request);
        $this->setLayout(false);
        $this->setTemplate('viewEmployeeList', 'pim');
    }
}
