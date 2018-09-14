<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 28/8/18
 * Time: 12:13 PM
 */
class accessEmployeeDataAction extends sfAction
{
    /**
     * Execute any application/business logic for this component.
     *
     * In a typical database-driven application, execute() handles application
     * logic itself and then proceeds to create a model instance. Once the model
     * instance is initialized it handles all business logic for the action.
     *
     * A model should represent an entity in your application. This could be a
     * user account, a shopping cart, or even a something as simple as a
     * single product.
     *
     * @param sfRequest $request The current sfRequest object
     *
     * @return mixed     A string containing the view name associated with this action
     */
    public function execute($request)
    {
        // TODO: Implement execute() method.

        $data = $request->getParameterHolder()->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $empNumber = $data['employee']['empId'];
                $employee = $this->getEmployeeService()->getEmployee($empNumber);

                if (empty($employee)) {
//                    $this->getUser()->setFlash('success', __(TopLevelMessages::SELECT_RECORDS));
                } else {

                    $this->getGetAllEmployeeRecordsService()->getAllEmployeeRecords($empNumber);
                }
            } catch (Exception $e) {
                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_FAILURE));
            }
        }
        $this->form = new GetAllDataForm();
    }

    public function getEmployeeService()
    {
        if (!isset($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    public function getGetAllEmployeeRecordsService()
    {
        if (!isset($this->getAllEmployeeRecordsService)) {
            $this->getAllEmployeeRecordsService = new GetAllEmployeeRecordsService();
        }
        return $this->getAllEmployeeRecordsService;
    }


}