<?php

class saveDepartmentAction extends sfAction{

    private $departmentService;

    public function getDepartmentService() {
        if (is_null($this->departmentService)) {
            $this->departmentService = new DepartmentService();
            $this->departmentService->setDepartmentDao(new DepartmentDao());
        }
        return $this->departmentService;
    }

    public function setDepartmentService(DepartmentService $departmentService) {
        $this->departmentService = $departmentService;
    }

    public function execute($request) {
         $id = trim($request->getParameter('hdnId'));
        $name = trim($request->getParameter('txtName'));
        $description = trim($request->getParameter('txtDescription'));
        $unitId = trim($request->getParameter('txtUnit_Id'));

        $parentId = trim($request->getParameter('hdnParent', null));

        $department = new Department();
        $department->setId($id);
        $department->setName($name);
        $department->setDescription($description);
        $department->setUnitId($unitId);

        $object = new stdClass();
        try {
            if (empty($parentId)) {
                $result = $this->getDepartmentService()->saveDepartment($department);
            } else {
                $parentDepartment = $this->getDepartmentService()->readDepartment($parentId);
                $result = $this->getDepartmentService()->addDepartment($parentDepartment, $department);
            }

            if ($result) {
                $object->affectedId = $department->getId();
                $object->messageType = 'success';
                $object->message = 'Department was saved successfully';
            } else {
                $object->messageType = 'failure';
                $object->message = 'Failed to save department';
            }
        } catch (Exception $e) {
            $object->messageType = 'failure';
            $object->message = ErrorMessageResolver::resolveMessage($e);
        }

        @ob_clean();
        echo json_encode($object);
        exit;
    }
}

