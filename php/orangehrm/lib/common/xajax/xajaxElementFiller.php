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

class xajaxElementFiller {

	private $defaultOption = "Select";

	function xajaxElementFiller() {
	}

	function setDefaultOptionName($defaultOption) {
		$this->defaultOption = $defaultOption;
	}
	function cmbFiller ($objResponse,$fillArr,$fele,$form,$element,$defSel=1) {

		if($defSel == 1) {
	 		$objResponse->addScript("document.".$form.".".$element.".options[0] = new Option('--" . $this->defaultOption . "--','0');");
			$objResponse->addScript("document.".$form.".".$element.".options.length = 0;");
		} else if($defSel == 0) {
			$objResponse->addScript("document.".$form.".".$element.".options.length = 0;");
		} else {
			$objResponse->addScript("document.".$form.".".$element.".options.length = 1;");
		}

	 	for ($i=0;$fillArr && count($fillArr)>$i;$i++) {
            $optionText = CommonFunctions::escapeForJavascript($fillArr[$i][($fele+1)]);
            $optionValue = CommonFunctions::escapeForJavascript($fillArr[$i][$fele]);
	 	    $objResponse->addScript("document.".$form.".".$element.".options[".($defSel == 3 ? $i+1 : $i)."] = new Option('" . $optionText . "','". $optionValue ."');");
        }

		return $objResponse;
	}
	function cmbFiller2 ($objResponse,$fillArr,$nameIdex, $valueIndex, $form,$element,$defSel=1) {

		if($defSel == 1) {
	 		$objResponse->addScript("document.".$form.".".$element.".options[0] = new Option('--" . $this->defaultOption . "--','0');");
			$objResponse->addScript("document.".$form.".".$element.".options.length = 1;");
		} else if($defSel == 0) {
			$objResponse->addScript("document.".$form.".".$element.".options.length = 0;");
		} else {
			$objResponse->addScript("document.".$form.".".$element.".options.length = 1;");
		}

	 	for($i=0;$fillArr && count($fillArr)>$i;$i++) {
            $optionText = CommonFunctions::escapeForJavascript($fillArr[$i][($nameIdex)]);
            $optionValue = CommonFunctions::escapeForJavascript($fillArr[$i][$valueIndex]);
	 		$objResponse->addScript("document.".$form.".".$element.".options[".($defSel == 1 ? $i+1 : $i)."] = new Option('" .$optionText. "','".$optionValue."');");
         }
		return $objResponse;
	}

	function cmbFillerById ($objResponse,$fillArr,$fele,$form,$element,$defSel=-1) {

		if($defSel == -1) {
			$objResponse->addScript("document.getElementById('".$element."').options.length = 0;");
	 		$objResponse->addScript("document.getElementById('".$element."').options[0] = new Option('--" . $this->defaultOption . "--','0');");
		} else {
			$objResponse->addScript("document.getElementById('".$element."').options.length = $defSel;");
		}
	 	for($i=0;$fillArr && count($fillArr)>$i;$i++) {
            $optionText = CommonFunctions::escapeForJavascript($fillArr[$i][($fele+1)]);
            $optionValue = CommonFunctions::escapeForJavascript($fillArr[$i][$fele]);
	 		$objResponse->addScript("document.getElementById('".$element."').options[".($defSel == 1 ? $i+1 : $i)."] = new Option('" . $optionText. "','".$optionValue."');");
        }

		return $objResponse;
	}
}
?>
