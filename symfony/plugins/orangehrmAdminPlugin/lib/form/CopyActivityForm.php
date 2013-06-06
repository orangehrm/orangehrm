<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CopyActivityForm
 *
 * @author orangehrm
 */
class CopyActivityForm extends BaseForm {

	/**
	 *
	 */
	public function configure() {
		
		$this->widgetSchema->setNameFormat('copyActivity[%s]');
	}
}

?>
