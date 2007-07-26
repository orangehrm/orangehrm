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

class CommonFunctions {

	const COMMONFUNCTIONS_PAGE_NUMBER_LIMIT = 5;

	public function __construct() {
		//nothing to do
	}

	public function formatSciNO($sciNO) {
		return $sciNO;
	}

	/**
	 * function to Partition the Strings;
	 * $String  = The string that should be passed to explode
	 * $explodedString = the String that is exploded -- This is will return an Array
	 */
	public function explodeString($string, $explodeVal, $length=3) {

		if (!empty($explodeVal)) {
			$explodedString=explode($explodeVal,$string);
		} else {
			$explodedString=$string;
		}

		if (isset($explodedString[1])) {
			$str = (int)$explodedString[1] + 1;
		}	else if (isset($explodedString[0])) {
			$str = $explodedString[0]+1;
		} else {
			$str = 1;
		}
		//echo

		if (strlen($str) > 0) {
			$zeroLength = $length-strlen($str);

			if ($zeroLength < 0) {
				$zeroLength = 0;
			}

			return  $explodeVal . str_repeat("0", $zeroLength). $str;
		} else {
			return $explodeVal .  $str;
		}
	}

	public function explodeStringNumbers($string) {

		if ($string=='') {
			$string = 1;
			return $string;
		}else {
			return $string + 1;
		}
	}

	public function printPageLinks($recordCount, $currentPage) {

		$sysConst = new sysConf();
		$strpagedump= "" ;

		if ($recordCount) {
	    	$recCount = $recordCount;
		} else {
			$recCount = 0;
		}

		$noPages = (int) ($recCount / $sysConst->itemsPerPage);

		if($recCount%$sysConst->itemsPerPage)
		   $noPages++;

		if ($noPages > 1) {

			if($currentPage == 1) {
				$strpagedump .= "<font color='Gray'>#first</font>";
		    	$strpagedump .= "  ";
				$strpagedump .= "<font color='Gray'>#previous</font>";
			} else {
	    		$strpagedump .= "<a href='javascript:chgPage(1);'>#first</a>";
		    	$strpagedump .= "  ";
	    		$strpagedump .= "<a href='javascript:prevPage();'>#previous</a>";
			}

	    	$strpagedump .= "  ";

			$lowerLimit = (($currentPage - self::COMMONFUNCTIONS_PAGE_NUMBER_LIMIT) <= 0) ? 1 : ($currentPage - self::COMMONFUNCTIONS_PAGE_NUMBER_LIMIT);
			$c = $lowerLimit;
			while($c < $currentPage) {
	    		$strpagedump .= "<a href='javascript:chgPage(" .$c. ");'>" .$c. "</a>";
		    	$strpagedump .= "  ";
				$c++;
			}

	    	$strpagedump .= "  ". $currentPage . "  ";


			$upperLimit = (($currentPage + self::COMMONFUNCTIONS_PAGE_NUMBER_LIMIT) >= $noPages) ? $noPages : ($currentPage + self::COMMONFUNCTIONS_PAGE_NUMBER_LIMIT);
			$c = $currentPage + 1;
			while($c <=  $upperLimit) {
	    		$strpagedump .= "<a href='javascript:chgPage(" .$c. ");'>" .$c. "</a>";
		    	$strpagedump .= "  ";
			    $c++;
			}

			if ($currentPage == $noPages) {
				$strpagedump .= "<font color='Gray'>#next</font>";
		    	$strpagedump .= "  ";
				$strpagedump .= "<font color='Gray'>#last</font>";
			} else {
	    		$strpagedump .= "<a href='javascript:nextPage();'>#next</a>";
		    	$strpagedump .= "  ";
	    		$strpagedump .= "<a href='javascript:chgPage(" .$noPages. ");'>#last</a>";
			}
		}

		return $strpagedump;
	}

	/**
	 * Returns the css class by looking at the message.
	 * Works with standard message strings like "UPDATE_SUCCESS", "ADD_FAILURE" etc.
	 * Simply returns the last part of the string, in lower case.
	 * Ex: For "UPDATE_SUCCESS" returns "success".
	 *
	 * @param string $message The message
	 * @return string css class
	 */
	public static function getCssClassForMessage($message) {
		$class = null;

		if (!empty($message)) {
			$expString = explode("_", $message);
			$class = array_pop($expString);
		}
		return empty($class) ? "" : strtolower($class);
	}

	/**
	 * Converts the given time in seconds to decimal hours, with the given number of decimals
	 *
	 * @param int $seconds The number of seconds
	 * @param int $noOfDecimals The number of decimals
	 * @return string Formatted hours with the given decimal places
	 */
	 public static function getTimeInHours($seconds, $noOfDecimals = 2) {
		$hours = intVal($seconds) / 3600;
		$formattedHours = sprintf("%.{$noOfDecimals}f", $hours);
		return $formattedHours;
	 }

	/**
	 * Function to check if the given variable is a valid id
	 *
	 * both pure ints and strings with leading zeros (ex: 012) are
	 * considered valid.
	 *
	 * NOTE: Considers negative numbers as invalid id's. Valid Id's
	 * should be positive integers.
	 *
	 * @param mixed id
	 * @return bool true if a valid id, false otherwise
	 */
	public static function isValidId($id) {

		if (is_int($id) && (intVal($id) >= 0)) {
			return true;
		}

		/*
		 * Trim leading zeros if a string and check that it is not a float.
		 */
		if (is_string($id)) {

			if ((preg_match('/^[0-9]+$/', $id)) && (intval($id) >= 0)) {

				return true;
			}
		}

		return false;
	}
}
?>