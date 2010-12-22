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
 * ListSorter 
 */
class ListSorter {
	
	const ASCENDING = 'ASC';
	const DESCENDING = 'DESC';
	
	protected $sessionVarName;
	protected $nameSpace;
	protected $sort;
	 
	protected $sortField = null;
	protected $sortOrder = null;
	protected $sortUrl = null;
	
	protected $user;
	
	/** Set via config */ 
	protected $desc_class;
	protected $asc_class;
	protected $default_class;
		 
	/**
	 * Constructor
	 */
	public function __construct($sessionVarName, $nameSpace, $user, $defaultSort) {
	    $this->sessionVarName = $sessionVarName;
	    $this->nameSpace = $nameSpace;	    
	    
	    $sort = $user->getAttribute($sessionVarName, null, $nameSpace);	    
	    $this->sort = is_null($sort) ? $defaultSort : $sort;
	    
	    $this->user = $user;
	    
	    $this->desc_class = sfConfig::get('app_sort_desc_class');
	    $this->asc_class = sfConfig::get('app_sort_asc_class');
	    $this->default_class = sfConfig::get('app_sort_default_class');
	    
	}	

    public function setSort(array $sort) {
        if (!is_null($sort[0]) && is_null($sort[1])) {
            $sort[1] = self::ASCENDING;
        }
        $this->sort = $sort;
        $this->user->setAttribute($this->sessionVarName, $sort, $this->nameSpace);
    }
        
    public function getSort() {
        return $this->sort;
    }
    
	public function sortLink($fieldName, $displayName = null, $url, $attributes = array(),$extraParam = '') {

		$class = $this->default_class;
		$nextOrder = self::ASCENDING;	

		/* Default order to Ascending and change if sorted ascending in current page */		
		if ($this->sort[0] === $fieldName) {
	
		    if ($this->sort[1] === self::ASCENDING) {
		        $nextOrder = self::DESCENDING;
		        $class = $this->asc_class;
			} else if ($this->sort[1] == self::DESCENDING) {
			    $class = $this->desc_class;
			} 
		} 
		$title = empty($displayName) ? $fieldName : $displayName;
		
		$attributes['class'] = $class;		
		$url .= '?sort=' . $fieldName . '&order=' . $nextOrder;	
		if($extraParam !='')
			$url .= '&'.$extraParam;	
		return link_to($title, $url, $attributes);
	}
}
