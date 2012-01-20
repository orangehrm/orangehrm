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

require_once ROOT_PATH.'/lib/dao/DMLFunctions.php';
require_once ROOT_PATH.'/lib/dao/SQLQBuilder.php';

/**
 */

class MenuItem {

	private $menuId;
	private $icon;
	private $menuText;
	private $link;
	private $subMenuItems;
	private $target; // target window for link
	private $enabled;
	private $current;
	
	public function __construct($icon = null, $menuText = null, $link = null, $target = null, $enabled = true, $current = false) {
		$this->icon = $icon;
		$this->menuText = $menuText;
		$this->link = $link;
		$this->target = $target;
		$this->enabled = $enabled;
		$this->current = $current;
	}
	
	public function setCurrent($current) {
		$this->current = $current;
	}
	
	public function isCurrent() {
		return $this->current;
	}
	
	public function setEnabled($enabled) {
		$this->enabled = $enabled;
	}
	
	public function isEnabled() {
		return $this->enabled;
	}
	
    /**
     * Retrieves the value of menuId.
     * @return menuId
     */
    public function getMenuId() {
        return $this->menuId;
    }

    /**
     * Sets the value of menuId.
     * @param menuId
     */
    public function setMenuId($menuId) {
        $this->menuId = $menuId;
    }

    /**
     * Retrieves the value of icon.
     * @return icon
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * Sets the value of icon.
     * @param icon
     */
    public function setIcon($icon) {
        $this->icon = $icon;
    }

    /**
     * Retrieves the value of menuText.
     * @return menuText
     */
    public function getMenuText() {
        return htmlspecialchars($this->menuText, ENT_QUOTES);
    }

    /**
     * Sets the value of menuText.
     * @param menuText
     */
    public function setMenuText($menuText) {
        $this->menuText = $menuText;
    }

    /**
     * Retrieves the value of link.
     * @return link
     */
    public function getLink() {
    	
    	/** TODO: !!!!!!!!!! Instead of doing this, do the change in the code which sets the link */
    	$link = $this->link;
    	$link = str_replace('&', '&amp;', $link);
        return $link;
    }

    /**
     * Sets the value of link.
     * @param link
     */
    public function setLink($link) {
        $this->link = $link;
    }

    /**
     * Retrieves the value of subMenuItems.
     * @return subMenuItems
     */
    public function getSubMenuItems() {
        return $this->subMenuItems;
    }

    /**
     * Sets the value of subMenuItems.
     * @param subMenuItems
     */
    public function setSubMenuItems($subMenuItems) {
        $this->subMenuItems = $subMenuItems;
    }
    
    public function setTarget($target) {
    	$this->target = $target;
    }	

    public function getTarget() {
    	return $this->target;
    }	
}

?>
