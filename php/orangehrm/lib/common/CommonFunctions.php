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


/** function to Partition the Strings; 
$String  = The string that should be passed to explode
$explodedString = the String that is exploded -- This is will return an Array
*/

function explodeString($string,$explodeVal) {

	$explodedString  = explode($explodeVal,$string);		
		
		if (isset($explodedString[1])) {
				
			$str = (int)$explodedString[1] + 1;		
		}	else {
			$str = 1;	
		}
		//echo 
		
		if (strlen($str) == 1) {		
			return  $explodeVal . "00" . $str;
		} else if (strlen($str) == 2) {
			return  $explodeVal . "0" . $str;		
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