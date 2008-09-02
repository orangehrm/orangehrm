<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/


class EXTRACTOR_CompProperty {

    private $compProperty;
    private $editPropId, $editEmpId;

    function EXTRACTOR_CompProperty() {

        $this->compProperty = new CompProperty();
    }

    function parseAddData($postArr) {

        $this->compProperty->setPropName(CommonFunctions::escapeHtml($postArr['txtPropertyName']));

        return $this->compProperty;
    }

    function parseEditData($postArr) {

        if(isset($postArr['propId']))
            $this->compProperty->setEditPropIds($postArr['propId']);

        if(isset($postArr['cmbUserEmpID']))
            $this->compProperty->setEditEmpIds($postArr['cmbUserEmpID']);

        if(isset($postArr['id']))
            $this->compProperty->setEditPropIds($postArr['id']);

        if(isset($postArr['txtPropertyName']))
            $this->compProperty->setPropName(CommonFunctions::escapeHtml($postArr['txtPropertyName']));

        if(isset($postArr['capturemode'])=='propedit')
            $this->compProperty->setEditPropFlag(true);

        return $this->compProperty;
    }

    function parseDeleteData($postArr) {

        $list=array();

        $chkBoxes = $postArr['chkPropId'];

        foreach ($chkBoxes as $prop_id)
             $list[]= $prop_id;

        $this->compProperty->setDeleteList($list);

        return $this->compProperty;
    }

}
?>
