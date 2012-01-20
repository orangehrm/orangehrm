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

/**
 * Manages event handlers (listeners)
 * Mediator and Subject of observer pattern
 * 
 * NOTE 1: Does not work correctly if more than one observer (listening to the same event) attempts to change the UI flow.
 */
class EventMediator {
    const OBSERVER_DIR = 'observers';
    
    /* Known events */
    const POST_CUSTOM_FIELD_DELETE_EVENT = 'post_custom_field_delete';
    
    // Event Mediator singleton
    private static $instance = null;
    
    private $observers;    

    /**
     * Private constructor. Use instance() method to get singleton instance
     */
    private function __construct() {
        $this->_loadObservers();
    }

    /**
     * Get singleton instance of this class
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new EventMediator();
        }

        return self::$instance;
    }
    
    /**
     * Attach given observer. From now on, the passed observer will
     * be notified whenever the given event occurs
     * 
     * @param String $event Event name
     * @param EventObserver Observer object
     */
    public function attach($event, $observer) {
        if (isset($this->observers[$event])) {
            $eventListeners = $this->observers[$event];            
        } else {
            $eventListeners = array();            
        }
        $eventListeners[spl_object_hash($observer)] = $observer;
        $this->observers[$event] = $eventListeners;
    }
    
    
    public function detach($observer) {
        // not implemented    
    }
    
    /**
     * Notify listeners to the given event.
     * 
     * NOTE: Does not work correctly if more than one observer (listening to the same event) attempts to change the UI flow. 
     * redirect the user to a confirmation screen.
     *  
     * @param String $event Event name
     * @param Array $data Array containing event specific data
     * @return boolean true if caller should continue UI flow, false if caller should exit 
     *      (typically done when observer handles the UI - eg redirecting the user to a confirmation page)
     */
    public function notify($event, $data = array()) {      
        $result = true;
         
        if (isset($this->observers[$event])) {
            $eventListeners = $this->observers[$event];
            foreach ($eventListeners as $listener) {
                $result = $result && $listener->notify($event, $data);
            }
        }
        
        return $result;
    }
    
    /**
     * Loads observer classes
     */
    private function _loadObservers() {
        
        $observersDir = rtrim(ROOT_PATH, '/') . '/lib/' . self::OBSERVER_DIR;
        if (is_dir($observersDir)) {
        	$observers = $this->_listFiles($observersDir);
	        foreach ($observers as $observer) {
	            if (is_file($observer)) {
	                $fileInfo = pathinfo($observer);
	                $className = $fileInfo['basename'];
	                $extension = $fileInfo['extension'];
	                if ($extension === 'php') {
	                   $className = str_replace("." . $extension, "", $className);
	                    require_once $observer;
	                    $object = new $className;
	                    
	                    if ($object instanceof EventObserver) {
	                        $object->register($this);
	                    }
	                }
	            }
	        }
        }
        
    }
    
    /**
     * List all files (including directories) under the given directory.
     * 
     * @param String $path Directory to look in
     * @return Array Array of file/directory names
     */   
    private function _listFiles($path) {
        
        $files = array();
        
        $path = rtrim($path, '/').'/';  
        if (is_readable($path)) {
            $items = (array) glob($path.'*');

            foreach ($items as $index => $item) {
                $files[] = str_replace('\\', '/', $item);
            }
        }
        return $files;       
    }
}