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

require_once ROOT_PATH.'/lib/confs/sysConf.php';

class CommonFunctions {

	const COMMONFUNCTIONS_PAGE_NUMBER_LIMIT = 5;

	public function __construct() {
		//nothing to do
	}

	public function formatSciNO($sciNO) {
		return $sciNO;
	}

	public static function extractNumericId($strId) {
		$id = preg_replace('/^[A-Z]*([0-9]*)$/', '$1', $strId);
		return $id;
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

	/**
	 * Format a time period given in minutes as hours and minutes.
	 *
	 * Ex: $minutes = 70 gives output = "1h 10m"
	 *
	 * @param int $minutes Time period in minutes
	 * @param string $zeroValue The value to be shown for zero minutes (optional)
	 * @param string $minuteSymbol Symbol for minutes
	 * @param string $hourSymbol Hour symbol
	 * @return formatted string in hours and minutes
	 */
	public static function formatMinutesAsHoursAndMinutes($minutes, $zeroValue = "0h", $minuteSymbol = "m", $hourSymbol = "h") {

		$minus = false;
		$formattedVal = "";

		$minutes = round($minutes);

		if ($minutes == 0) {
			return $zeroValue;
		} else if ($minutes < 0) {
			$minus = true;
			$minutes = abs($minutes);
			$formattedVal = "-";
		}

		$hours = floor($minutes / 60);

		$minutesLeft = $minutes % 60;

		if ($hours > 0) {
			$formattedVal .= "{$hours}{$hourSymbol} ";
		}
		if ($minutesLeft > 0) {
			$formattedVal .= "{$minutesLeft}{$minuteSymbol}";
		}

		return trim($formattedVal);
	}

	/**
	 * Returns the theme directory
	 * @return Theme directory
	 */
	public static function getTheme() {

		// Look in request variables
		if (isset($_REQUEST['styleSheet']) && !empty($_REQUEST['styleSheet'])) {
			$requestParam = $_REQUEST['styleSheet'];

			// If found, validate
			$themePath = ROOT_PATH . '/themes/' . $requestParam;
			if (file_exists($themePath)) {
				return $requestParam;
			}
		}

		// Look in session
		if (isset($_SESSION['styleSheet']) && !empty($_SESSION['styleSheet'])) {
			return $_SESSION['styleSheet'];
		}

		// If not found yet, look in sysConf.php
		$sysConf = new sysConf();
		$sysConfSetting = $sysConf->getStyleSheet();
		if (!empty($sysConfSetting)) {
			return $sysConfSetting;
		}

		// If still not found, use default beyondT
		return 'beyondT';
	}

	/**
	 * String format the given number with SI unit prefix for units.
	 *
	 * Method has an accuracy of 2 d.p
	 *
	 * e.g. 1000 => 1K
	 *
	 * @param Integer number
	 * @return String formatted
	 */
	public static function formatSiUnitPrefix($number) {

		switch ($number) {
			case $number >= 1000000000000 : $prefix=" T";
										   $divisor=1000000000000;
										   break;
			case $number >= 1000000000 	 : $prefix=" G";
										   $divisor=1000000000;
										   break;
			case $number >= 1000000 	  	 : $prefix=" M";
										   $divisor=1000000;
										   break;
			case $number >= 1000 	  	 : $prefix=" k";
										   $divisor=1000;
										   break;
			default : $prefix="";
					  $divisor=1;
					  break;
		}
		$formatted=round(($number/$divisor), 2)."{$prefix}";

		return $formatted;
	}

	/**
	 * Checks two time periods for overlapping times
	 * Time values must be given in HH:MM 24 hour format
	 * eg: 08:20, 21:15 etc.
	 *
	 * @param string $start1 Start time of first time period
	 * @param string $end1 End time of first time period
	 * @param string $start2 Start time of second time period
	 * @param string $end2 End time of second time period
	 *
	 * @return boolean true if there is an overlap, false otherwise
	 *
	 * NOTE: Assumes time periods are correctly ordered (start time < end time)
	 */
	public static function checkTimeOverlap($start1, $end1, $start2, $end2) {

		// Replace : with .
		$startTime1 = str_replace(':', '.', $start1);
		$endTime1 = str_replace(':', '.', $end1);
		$startTime2 = str_replace(':', '.', $start2);
		$endTime2 = str_replace(':', '.', $end2);

		// Consider only correctly ordered time periods.
		if (($startTime1 < $endTime1) && ($startTime2 < $endTime2)) {
			if (($startTime1 < $startTime2) && ($endTime1 > $startTime2)) {
				return true;
			}
			if (($startTime2 < $startTime1) && ($endTime2 > $startTime1)) {
				return true;
			}
		}

		return false;

	}
}
?>