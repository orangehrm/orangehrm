<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrganizationDataProcessor
 *
 * @author chathura
 */
class OrganizationDataProcessor extends AbstractBaseProcessor {

    public function process($definition) {
        if (!isset($definition)) {
            return null;
        }
        $result = null;
        try {



            $datapoint = new SimpleXMLElement($definition);

            if ($datapoint['type'] . "" == 'organization') {

                $organizationService = new OrganizationService();

                $organizationObj = $organizationService->getOrganizationGeneralInformation();

                if ($organizationObj) {
                    $organizationArray = $organizationObj->toArray();
                    $columnName = trim($datapoint->parameters->column . "");
                    $name = $datapoint->settings->name;

                    $result = $organizationArray[$columnName];
                }
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $result;
    }

//put your code here
}
