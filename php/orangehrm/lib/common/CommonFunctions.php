<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

class CommonFunctions {

function CommonFunctions() {

}


function formatSciNO($sciNO) {

	return $sciNO;
	//return sprintf("%1.0f",$sciNO);
}


/** function to Partition the Strings;
$String  = The string that should be passed to explode
$explodedString = the String that is exploded -- This is will return an Array
*/

function explodeString($string, $explodeVal, $length=3) {

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



 function explodeStringNumbers($string) {

		if ($string=='') {
			$string = 1;
			return $string;

		}else {
			 return $string + 1;
	    }

  }

}
?>