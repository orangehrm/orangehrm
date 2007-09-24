<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

require_once ROOT_PATH . '/lib/common/Language.php';

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
		require_once ROOT_PATH . '/language/default/lang_default_full.php';

		$lan = new Language();
		require_once($lan->getLangPath("full.php"));

		$records = $this->getObj();

		if (isset($_SESSION['styleSheet']) && !empty($_SESSION['styleSheet'])) {
			$styleSheet = $_SESSION['styleSheet'];
		} else {
			$styleSheet = "beyondT";
		}
		require_once ROOT_PATH.$this->getTemplateHeader();
		require_once ROOT_PATH.$this->getTemplatePath();
		require_once ROOT_PATH.$this->getTemplateFooter();
	}
}
?>