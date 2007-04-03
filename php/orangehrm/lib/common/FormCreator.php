<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 hSenid Software, http://www.hsenid.com
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

		require_once(ROOT_PATH.$this->formPath);
	}
}
?>
