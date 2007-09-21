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
 *
 */

require_once ROOT_PATH . '/lib/confs/sysConf.php';

/**
 * Class to handle locale specific formatting tasks
 * Implemented as a singleton.
 */
 class LocaleUtil {

	/** This singleton instance */
	private static $instance;

	private $sysConf;

	/**
	 * Set the sysConf instance used by this object.
	 *
	 * @param sysConf $sysConf sysConf object
	 */
	public function setSysConf($sysConf) {
		$this->sysConf = $sysConf;
	}

	/**
	 * Private construct
	 */
	private function __construct() {
		$this->sysConf = new sysConf();
	}

	/**
	 * Get the singleton instance of this class
	 */
	 public static function getInstance() {

	 	if (!is_a(self::$instance, 'LocaleUtil')) {
	 		self::$instance = new LocaleUtil();
	 	}

		return self::$instance;
	 }

	 /**
	  * Converts the date format to the format configured in the system
	  *
	  * @param string date
	  * @return date formatted according to configured format
	  */
	 public function formatDate($date, $customFormat=null) {

	 	 if (empty($date)) {
	 	 	return "";
	 	 }

		 $timeStamp = strtotime($date);
		 $format = $this->sysConf->getDateFormat();

		 if (empty($customFormat)) {

			 if (empty($format)) {
			 	return $date;
			 }
		 	$formattedValue = date($format, $timeStamp);
		 } else {
		 	$formattedValue = date($customFormat, $timeStamp);
		 }

		 return $formattedValue;
	 }

	/**
	  * Converts the time format to the format configured in the system
	  *
	  * @param string time
	  * @return time formatted according to configured format
	  */
	 public function formatTime($time, $customFormat=null) {

	 	 if (empty($time)) {
	 	 	return "";
	 	 }

		 $timeStamp = strtotime($time);
		 $format = $this->sysConf->getTimeFormat();

		 if (empty($customFormat)) {

			 if (empty($format)) {
			 	return $time;
			 }
		 	$formattedValue = date($format, $timeStamp);
		 } else {
		 	$formattedValue = date($customFormat, $timeStamp);
		 }

		 return $formattedValue;
	 }

	 /**
	  * Converts a date and time value to the format configured in the system
	  *
	  * @param string date and time to be converted
	  * @param string customFormat Custom date time format to be used instead of the system configured format
	  * @return date and time formatted according to configured format
	  */
	 public function formatDateTime($dateTime, $customFormat=null) {

	 	 if (empty($dateTime)) {
	 	 	return "";
	 	 }

		 $timeStamp = strtotime($dateTime);
		 $dateFormat = $this->sysConf->getDateFormat();
		 $timeFormat = $this->sysConf->getTimeFormat();

		 if (empty($customFormat)) {

			 if (empty($dateFormat) || empty($timeFormat)) {
			 	return $dateTime;
			 }
			 $format = $dateFormat . " " . $timeFormat;
		 } else {
			 $format = $customFormat;
		 }

		 $formattedValue = date($format, $timeStamp);

		 return $formattedValue;
	 }

	 /**
	  * String date will be converted from the custom format to YYYY-mm-dd
	  *
	  * Right now only English dates will be convered.
	  *
	  * @todo TODO The method should be extended to support date of a given format.
	  *
	  * @param String date
	  * @param String customFormat(Optional)
	  * @return String standardDate
	  */
	 public static function convertToStandardDateFormat($date, $customFormat=null) {
	 	if ($customFormat == null) {
	 		$sysConf = new sysConf();
	 		$format = $sysConf->getDateFormat();
	 	}
	 	$standardDate = date('Y-m-d', strtotime($date));

	 	return $standardDate;
	 }

	 public static function convertToXpDateFormat($dateFormat) {
		$map = array(// Day
					 'd'=>'dd',
					 'j'=>'d',
					 // Month
					 'm'=>'MM',
					 'n'=>'M',
					 // Year
					 'Y'=>'yyyy',
					 'y'=>'yy',
					 // Hours
					 'H'=>'HH',
					 'h'=>'hh',
					 'G'=>'H',
					 'g'=>'h',
					 // Minutes
					 'i'=>'mm',
					 // Seconds
					 's'=>'ss');

		$chars = str_split($dateFormat, 1);
		$conv = '';

		for ($i=0; $i<count($chars); $i++) {
			if (isset($map[$chars[$i]])) {
				$conv.=$map[$chars[$i]];
			} else {
				$conv.=$chars[$i];
			}
		}

		return $conv;
	}
}

class LocaleException extends Exception {
}

?>
