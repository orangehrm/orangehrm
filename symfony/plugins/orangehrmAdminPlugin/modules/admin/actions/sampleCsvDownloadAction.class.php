<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sampleCsvDownloadAction
 *
 * @author orangehrm
 */
class sampleCsvDownloadAction extends sfAction {

	public function execute($request) {

		$response = $this->getResponse();
		$response->setHttpHeader('Pragma', 'public');
		$response->setHttpHeader("Content-type", "application/csv");
		$response->setHttpHeader("Content-Disposition", "attachment; filename=importData.csv");
		$response->setHttpHeader('Expires', '0');
		$content = "first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email";
		$response->setHttpHeader("Content-Length", strlen($content));
		$response->setContent($content);

		return sfView::NONE;
	} 

}

?>
