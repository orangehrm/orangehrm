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
		require_once ROOT_PATH . '/language/default/lang_default_full.php';

		$lan = new Language();
		require_once($lan->getLangPath("full.php"));
		$fileName = pathinfo($this->formPath, PATHINFO_BASENAME);

		if (preg_match('/view\.php$/', $fileName) == 1) {
			require_once($lan->getLangPath($fileName));
		}

		require_once ROOT_PATH . $this->formPath;

	}
}
?>
