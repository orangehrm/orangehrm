<?php

class saveSubunitAction extends sfAction {

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
        
        $form = new SubunitForm(array(),array(),true);
        $form->bind($request->getParameter($form->getName()));
        
        if ($form->getCSRFToken() == $request->getParameter('_csrf_token')) {
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
                    $parentSubunit = $this->getCompanyStructureService()->getSubunitById($parentId);
                    $result = $this->getCompanyStructureService()->addSubunit($parentSubunit, $subunit);
                }

                if ($result) {
                    $object->affectedId = $subunit->getId();
                    $object->messageType = 'success';
                    $object->message = __(TopLevelMessages::SAVE_SUCCESS);
                } else {
                    $object->messageType = 'warning';
                    $object->message = __('Failed to Save');
                }
            } catch (Exception $e) {
                $object->messageType = 'warning';
                $object->message = __('Name Already Exists');
            }

            @ob_clean();
            return $this->renderText(json_encode($object));
        }
        
        
    }

}

