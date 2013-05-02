<?php

class AttendanceRecordHeaderFactory extends ohrmListConfigurationFactory {

    protected function init() {

        $header1 = new ListHeader();
        $header2 = new RawLabelCellHeader();
        $header3 = new ListHeader();
        $header4 = new RawLabelCellHeader();
        $header5 = new ListHeader();
        $header6 = new ListHeader();
        $header7 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Employee Name',
            'width' => '20%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => array('getEmployee', 'getFirstAndLastNames')),
        ));

        $header2->populateFromArray(array(
            'name' => 'Punch In',
            'width' => '20%',
            'elementType' => 'rawLabel',
            'elementProperty' => array('getter' => 'getPunchInUserTimeAndZone'),
        ));
        
        $header3->populateFromArray(array(
            'name' => 'Punch In Note',
            'width' => '15%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getPunchInNote'),
        ));
        
        $header4->populateFromArray(array(
            'name' => 'Punch Out',
            'width' => '20%',
            'elementType' => 'rawLabel',
            'elementProperty' => array('getter' => 'getPunchOutUserTimeAndZone'),
        ));
        
        $header5->populateFromArray(array(
            'name' => 'Punch Out Note',
            'width' => '15%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getPunchOutNote'),
        ));
        
        $header6->populateFromArray(array(
            'name' => 'Duration(Hours)',
            'width' => '5%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getDuration'),
        ));
        
        $header7->populateFromArray(array(
            'name' => 'Total',
            'width' => '5%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getTotal'),
        ));

        $this->headers = array($header1, $header2, $header3, $header4, $header5, $header6, $header7);
    }

    public function getClassName() {
        return 'AttendanceRecordList';
    }

}