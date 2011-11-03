<?php

class saveSubunitAction extends sfAction{

   private $companyStructureService;

    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }

    public function execute($request) {
         $id = trim($request->getParameter('hdnId'));
        $name = trim($request->getParameter('txtName'));
        $description = trim($request->getParameter('txtDescription'));
        $unitId = trim($request->getParameter('txtUnit_Id'));

        $parentId = trim($request->getParameter('hdnParent', null));

        $subunit = new Subunit();
        $subunit->setId($id);
        $subunit->setName($name);
        $subunit->setDescription($description);
        $subunit->setUnitId($unitId);

        $object = new stdClass();
        try {
            if (empty($parentId)) {
                $result = $this->getCompanyStructureService()->saveSubunit($subunit);
            } else {
                $parentSubunit = $this->getCompanyStructureService()->getSubunit($parentId);
                $result = $this->getCompanyStructureService()->addSubunit($parentSubunit, $subunit);
            }

            if ($result) {
                $object->affectedId = $subunit->getId();
                $object->messageType = 'success';
                $object->message = __('Sub Unit Was Saved Successfully');
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

