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

	public function printPageLinks($recordCount, $currentPage, $recordsPerPage=null) {

		$sysConst = new sysConf();
		$strpagedump= "" ;

		if ($recordCount) {
	    	$recCount = $recordCount;
		} else {
			$recCount = 0;
		}

		if (isset($recordsPerPage)) {
		    $noPages = (int) ($recCount / $recordsPerPage);
		    $additionalPage = $recCount%$recordsPerPage;
		} else {
		    $noPages = (int) ($recCount / $sysConst->itemsPerPage);
		    $additionalPage = $recCount%$sysConst->itemsPerPage;
		}

		if($additionalPage)
		   $noPages++;

		if ($noPages > 1) {

			if($currentPage == 1) {
				$strpagedump .= '<span class="inactive">#first</span>';
		    	$strpagedump .= '&nbsp;';
				$strpagedump .= '<span class="inactive">#previous</span>';
			} else {
	    		$strpagedump .= '<a href="javascript:chgPage(1);">#first</a>';
		    	$strpagedump .= '&nbsp;';
	    		$strpagedump .= '<a href="javascript:prevPage();">#previous</a>';
			}

	    	$strpagedump .= "  ";

			$lowerLimit = (($currentPage - self::COMMONFUNCTIONS_PAGE_NUMBER_LIMIT) <= 0) ? 1 : ($currentPage - self::COMMONFUNCTIONS_PAGE_NUMBER_LIMIT);
			$c = $lowerLimit;
			while($c < $currentPage) {
	    		$strpagedump .= '<a href="javascript:chgPage(' . $c . ');">' .$c. '</a>';
		    	$strpagedump .= '&nbsp;';
				$c++;
			}

	    	$strpagedump .= '<span class="inactive">' . $currentPage . '</span> ';


			$upperLimit = (($currentPage + self::COMMONFUNCTIONS_PAGE_NUMBER_LIMIT) >= $noPages) ? $noPages : ($currentPage + self::COMMONFUNCTIONS_PAGE_NUMBER_LIMIT);
			$c = $currentPage + 1;
			while($c <=  $upperLimit) {
	    		$strpagedump .= '<a href="javascript:chgPage(' . $c . ');">' . $c . '</a>';
		    	$strpagedump .= '&nbsp;';
			    $c++;
			}

			if ($currentPage == $noPages) {
				$strpagedump .= '<span class="inactive">#next</span>';
		    	$strpagedump .= '&nbsp;';
				$strpagedump .= '<span class="inactive">#last</span>';
			} else {
	    		$strpagedump .= '<a href="javascript:nextPage();">#next</a>';
		    	$strpagedump .= '&nbsp;';
	    		$strpagedump .= '<a href="javascript:chgPage(' . $noPages . ');">#last</a>';
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
	public static function getCssClassForMessage($message, $default='') {
		$class = null;

		if (!empty($message)) {
			$expString = explode("_", $message);
			$class = array_pop($expString);
		}
		return empty($class) ? $default : strtolower($class);
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
	 * Function to check if passed value is an int.
	 * Works even with string parameters unlike php's is_int. And supports plus/minus as a prefix
	 *
	 * @param string/int value to check
	 * @param boolean allowSign Whether sign (+ or -) should be allowed
	 * @return boolean true if an integer false otherwise
	 */
	public static function isInt($value, $allowSign = false) {

		if ($allowSign) {
			$regExp = '/^(\+|\-)[0-9]+$/';
		} else {
			$regExp = '/^[0-9]+$/';
		}

		return (preg_match($regExp, $value) > 0) ? true : false;
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
     * With the optional prefix parameter, id's like LOC001 can also be
     * tested.
     *
	 * @param mixed id
     * @param String $prefix (Optional prefix)
	 * @return bool true if a valid id, false otherwise
	 */
	public static function isValidId($id, $prefix = null) {

        /* Check for prefix and remove it */
        if (!empty($prefix)) {
            if (strpos($id, $prefix) === 0) {
                $id = substr($id, strlen($prefix));
            } else {
                return false;
            }
        }

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

    /**
     * Escapes any special characters to html entities and make it safe for including into a web page.
     *
     * @param string $value Value to escape
     * @return string Escaped value
     */
    public static function escapeHtml($value) {
        return htmlspecialchars($value, ENT_QUOTES);
    }

	/**
	 * Escape string for use in javascript
	 *
	 * Escapes characters \, ", ' in the string by adding a \ in front.
     *
     * (based on http://code.google.com/p/doctype/wiki/ArticleXSSInJavaScript)
	 *
	 * @param String $string String to be escaped
	 * @return String escaped string
	 */
	public static function escapeForJavascript($string) {
		$charArray = str_split($string);
		$escapedString = '';

		foreach($charArray as $char) {
			switch ($char) {
				case "'":
					$escapedString .= "\\x27";
					break;
				case "\"":
					$escapedString .= "\\x22";
					break;
				case '\\':
					$escapedString .= "\\\\";
					break;
				case "\n":
					$escapedString .= "\\n";
					break;
				case "\r":
					$escapedString .= "\\r";
					break;
                case "\t":
                    $escapedString .= "\\t";
                    break;                    
				case "\f":
					$escapedString .= "\\f";
					break;
                case "&":
                    $escapedString .= "\\x26";
					break;
                case "<":
                    $escapedString .= "\\x3c";
					break;
                case ">": 
                    $escapedString .= "\\x3e";
					break;
                case "=":
                    $escapedString .= "\\x3d";
					break;                    

				default :
					$escapedString .= $char;
					break;
			}
		}
		return $escapedString;
	}

    /**
     * Get's the first N characters of the given string, optionally appending the given suffix.
     *
     * Eg: if $text = 'This is a test' and $n = 7 and $suffix = '...'
     * gives: 'This is...'
     *
     * @param String $text to format
     * @param int $n Number of characters
     * @param String $suffix Optional suffix
     *
     * @return String formatted string
     */
    public static function getFirstNChars($text, $n, $suffix = '') {

        if (strlen($text) > $n) {
            $text = substr($text, 0, $n);
            if (!empty($suffix)) {
                $text .= $suffix;
            }
        }

        return $text;
    }

    /**
     * Escapes the given string value to make it safe to use in an SQL query.
     * Checks for and properly handles magic quotes gpc setting.
     *  
     * @static
     * @param  $value
     * @return string
     */
    public static function safeEscapeSQL($value) {

		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}

		$value = mysql_real_escape_string($value);

        return $value;
    }

    /**
     * Sanitize user input by optionally trimming, stripping tags and limiting input to maxLen.
     *
     * @static
     *
     * @param  $value Value to process
     * @param  $maxLen if greater than 0, value is limited to this length. (any characters after maxLen are removed) 
     * @param bool $trim If true, value is trimmed of whitespace
     * @param bool $stripTags if true, html tags are stripped using strip_tags
     * @return bool|string processed string.
     */
    public static function cleanParam($value, $maxLen = -1, $trim = true, $stripTags = true) {
        $sanitizedValue = $value;

        if ($trim) {
            $sanitizedValue = trim($sanitizedValue);
        }

        if ($stripTags) {
            $sanitizedValue = strip_tags($sanitizedValue);
        }

        if (($maxLen > 0) && (strlen($sanitizedValue) > $maxLen)) {
            $sanitizedValue = substr($sanitizedValue, 0, $maxLen);
        }
        return $sanitizedValue;
    }

    public static function cleanIntParam($value) {

        $returnValue = self::cleanParam($value);

        $returnValue = preg_replace("/[^0-9.]/", "", $returnValue);

        return $returnValue;
    }

    /**
     * Is web request from localhost?
     * @static
     * @return bool - true if request was from same computer as the server. false if not.
     */
    public static function isRequestFromLocalhost() {

        // based on check in symfony _dev.php files
        $local = in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'));

        return $local;
    }

    /**
     * Should editing of sendmail path
     * @static
     * @return bool
     */
    public static function allowSendmailPathEdit() {

        $allow = false;
        $sysConf = new sysConf();

        /* Allow editing if from localhost or we have allowed editing from outside localhost */        
        if ($sysConf->allowSendmailPathEdit() &&
                    (CommonFunctions::isRequestFromLocalhost() || !$sysConf->sendmailPathEditOnlyFromLocalHost()) ) {
            $allow = true;
        }

        return $allow;
    }

}
?>
