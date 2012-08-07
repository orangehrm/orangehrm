<?php

class EmployeeListConfigurationFactory extends ohrmListConfigurationFactory {

    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new ListHeader();
        $header2 = new ListHeader();
        $header3 = new ListHeader();
        $header4 = new ListHeader();
        $header5 = new ListHeader();
        $header6 = new ListHeader();
        $header7 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Id',
            'width' => '5%',
            'isSortable' => true,
            'sortField' => 'employeeId',
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getEmployeeId'),
                'placeholderGetters' => array('id' => 'getEmpNumber'),
                'urlPattern' => public_path('index.php/pim/viewPersonalDetails/empNumber/{id}'),
            ),
        ));

        $header2->populateFromArray(array(
            'name' => __('First (& Middle) Name'),
            'width' => '20%',
            'isSortable' => true,
            'sortField' => 'firstMiddleName',
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getFirstAndMiddleName'),
                'placeholderGetters' => array('id' => 'getEmpNumber'),
                'urlPattern' => public_path('index.php/pim/viewPersonalDetails/empNumber/{id}'),
            ),
        ));

        $header3->populateFromArray(array(
            'name' => 'Last Name',
            'width' => '15%',
            'isSortable' => true,
            'sortField' => 'lastName',
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getFullLastName'),
                'placeholderGetters' => array('id' => 'getEmpNumber'),
                'urlPattern' => public_path('index.php/pim/viewPersonalDetails/empNumber/{id}'),
            ),
        ));

        $header4->populateFromArray(array(
            'name' => 'Job Title',
            'width' => '10%',
            'isSortable' => true,
            'sortField' => 'jobTitle',
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getJobTitleName')
        ));

        $header5->populateFromArray(array(
            'name' => 'Employment Status',
            'width' => '15%',
            'isSortable' => true,
            'sortField' => 'employeeStatus',
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getEmployeeStatus')
        ));

        $header6->populateFromArray(array(
            'name' => 'Sub Unit',
            'width' => '15%',
            'isSortable' => true,
            'sortField' => 'subDivision',
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getSubDivision')
        ));

        $header7->populateFromArray(array(
            'name' => 'Supervisor',
            'width' => '30%',
            'isSortable' => true,
            'sortField' => 'supervisor',
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getSupervisorNames')
        ));

        $this->headers = array($header1, $header2, $header3, $header4, $header5, $header6, $header7);
    }
    
    public function getClassName() {
        return 'Employee';
    }
}
