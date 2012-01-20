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

require_once ROOT_PATH . '/lib/common/EventMediator.php';
require_once ROOT_PATH . '/lib/common/EventObserver.php';
require_once ROOT_PATH . '/lib/common/AbstractObserver.php';

/**
 * Observer listening to changes of the custom fields
 */
class CustomFieldsDeletionObserver extends AbstractObserver implements EventObserver {

    /**
     * Constructor. Subscribes for required events 
     */
    public function __construct() {
    }
    
    /**
     * Register this observer with the passed subject
     * 
     * TODO: Instead of EventMediator, use an interface or abstract super class
     * 
     * @param EventMediator 
     */
    public function register($subject) {
        $subject->attach(EventMediator::POST_CUSTOM_FIELD_DELETE_EVENT, $this);      
    }
    
    /**
     * Notify of event
     * @param String $event Event name
     * @param Array $data Array containing event specific data
     * @return boolean true if caller should continue UI flow, false if observer redirected the user to 
     *      the confirmation page. 
     */
    public function notify($event, $data = array()) {

        switch ($event) {
            case EventMediator::POST_CUSTOM_FIELD_DELETE_EVENT:
                $action = 'cleanCustomFieldData';
                $module = 'pim';
                $occurance = 'custom_field_delete';
                break;
			
            default: 
                return true;                
        }

        if (isset($data['customFieldIds'])) {
            $actionParams = array($occurance, $data['customFieldIds']);
    		$controller = $this->createController($module);
 			$controller->$action($actionParams);
            
            return false;           
        }
        return true;
    }
    
}