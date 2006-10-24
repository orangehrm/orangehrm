<?php
class TemplateMerger {
	
	private $templatePath;
	private $templateHeader;
	private $templateFooter;
	private $obj;
	
	public function __construct($obj, $templatePath, $templateHeader='header.php', $templateFooter='footer.php') {
		
		$baseDir = pathinfo($templatePath, PATHINFO_DIRNAME);
		
		$this->setObj($obj);
		$this->setTemplatePath($templatePath);
		$this->setTemplateHeader($baseDir."/".$templateHeader);
		$this->setTemplateFooter($baseDir."/".$templateFooter);
		
	}
	
	public function setTemplatePath($path) {
		$this->templatePath = $path;
	}
	
	public function getTemplatePath() {
		return $this->templatePath;
	}
	
	public function setTemplateHeader($path) {
		$this->templateHeader = $path;
	}
	
	public function getTemplateHeader() {
		return $this->templateHeader;
	}
	
	public function setTemplateFooter($path) {
		$this->templateFooter = $path;
	}
	
	public function getTemplateFooter() {
		return $this->templateFooter;
	}
	
	public function setObj($obj) {
		$this->obj = $obj;
	}
	
	public function getObj() {
		return $this->obj;
	}
	
	public function display($modifier=null) {		

		require_once ROOT_PATH . '/lib/common/xajax/xajax.inc.php';
		require_once ROOT_PATH . '/lib/common/xajax/xajaxElementFiller.php';
		
		$records = $this->getObj();		
		
		require_once ROOT_PATH . $this->getTemplateHeader();
		require_once ROOT_PATH . $this->getTemplatePath();
		require_once ROOT_PATH . $this->getTemplateFooter();		
	}
}
?>