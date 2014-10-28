<?php
/*
 *
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
class OrangeHRMBaseException extends Exception
{
    
    public function __construct($message = "", $code = 0, $previous = NULL) {
        
        // $code is being cast to an int here because in some cases where we wrap an exception,
        // the wrapped exception can have a string $code, but the parent Exception class expects an int (or long?) 
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            parent::__construct($message, (int)$code);
        } else {
            parent::__construct($message, (int)$code, $previous);
        }
        
    }    
	
}