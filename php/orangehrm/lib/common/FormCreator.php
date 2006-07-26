<?php
//require_once OpenSourceEIM . '/lib/Exceptionhandling/ExceptionHandler.php';
require_once ROOT_PATH . '/lib/common/Language.php';

class FormCreator
{
	var $getArr;
	var $postArr;
	var $popArr;
	var $formPath;
	
	function FormCreator($getArr,$postArr = null) {
		
		$this->getArr = $getArr;
		if($postArr != null)
			$this->postArr = $postArr;
			
		$this->popArr = array();
	}
	
	function display() {
		$str = ROOT_PATH . $this->formPath;

		require_once ROOT_PATH . '/lib/common/xajax/xajax.inc.php';
		require_once ROOT_PATH . '/lib/common/xajax/xajaxElementFiller.php';

		
			$lan = new Language();
			if(!isset($this->getArr['mtcode']))
				require_once($lan->getLangPath(basename($this->formPath)));
			//print_r($lan->getLangPath(basename($this->formPath)));
		
		require_once ROOT_PATH . $this->formPath;		
		
	}
}