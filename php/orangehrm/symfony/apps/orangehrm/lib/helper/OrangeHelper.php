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

/**
 * Helper functions for orangehrm specific actions
 */
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

/**
 * Formats the employee number as expected by other components, by 
 * left padding with zeros if needed.
 * 
 * @param int $empNumber employee number
 * @return String $empNumber left padded with zeros
 */
function format_emp_number($empNumber) {
    
    $empIdLength = OrangeConfig::getInstance()->getSysConf()->getEmployeeIdLength();    
    $formattedNumber = str_pad($empNumber, $empIdLength, "0", STR_PAD_LEFT);
    return $formattedNumber;    
}

/**
 * 
 * @return String Html
 */
function message() {
	$html =	'';
	$user = sfContext::getInstance()->getUser();
	if($user->hasFlash('messageType') && $user->hasFlash('message') )
	{
		$class	=	'';
		switch( $user->getFlash('messageType') )
		{
			case 'SUCCESS':
				$class	=	'messageBalloon_success';
			break;
			
			case 'NOTICE':
				$class	=	'messageBalloon_notice';
			break;
			
			case 'WARNING':
				$class	=	'messageBalloon_warning';
			break;

			case 'FAILURE':
				$class	=	'messageBalloon_failure';
			break;

		}
		$html .=	"<div id='".$class."' class='".$class."'>";
		$html	.=	"<ul>";
            $messageList = $user->getFlash('message');
			foreach( $messageList as $message)
			{
				$html .= "<li>".$message."</li>";
			}
		$html	.=	"</ul>";
		$html	.=	"</div>";
		
	}
	
	return $html ;
}

/**
 * Uses the passed array for showing the message.
 * Doesn't use session data.
 */

function templateMessage($errors) {
	
	$html =	'';
	
	if (!empty($errors)) {
	
		$class = '';
		
		switch ($errors[0]) {
		    
			case 'SUCCESS': $class = 'messageBalloon_success';
							break;
			case 'NOTICE':	$class = 'messageBalloon_notice';
							break;
			case 'WARNING':	$class = 'messageBalloon_warning';
							break;
		    
		} 

		$html .= "<div id='".$class."' class='".$class."'>";

		$count = count($errors);

                // Only show a list if more than one error
                if ($count == 2) {
                    $html .= htmlspecialchars_decode($errors[1]);
                } else if ($count > 2) {
                    $html .= "<ul>";

                    $count = count($errors);

                    for ($i=1; $i<$count; $i++) {
                        $html .= "<li>".htmlspecialchars_decode($errors[$i])."</li>";
                    }

                    $html .= "</ul>";
                }
		$html .= "</div>";
	    
	}
	
	return $html;

}

function add_si_unit($number) {
    return CommonFunctions::formatSiUnitPrefix($number);
}

function formatDate($currentDate, $formatData) {
    
    $cDate = explode($formatData['currentSeparater'], $currentDate);
    $cFormat = explode($formatData['currentSeparater'], $formatData['currentFormat']);
    
    $cDateAssoc[$cFormat[0]] = $cDate[0];
    $cDateAssoc[$cFormat[1]] = $cDate[1];
    $cDateAssoc[$cFormat[2]] = $cDate[2];
    
    $nFormat = explode($formatData['newSeparater'], $formatData['newFormat']);
    
    $nDate[0] = $cDateAssoc[$nFormat[0]];
    $nDate[1] = $cDateAssoc[$nFormat[1]];
    $nDate[2] = $cDateAssoc[$nFormat[2]];
    
    return implode($formatData['newSeparater'], $nDate);
    
}


/**
 * @see CommonFunctions::escapeForJavascript
 */
function escapeForJavascript($string) {
    return CommonFunctions::escapeForJavascript($string);
}