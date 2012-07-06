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
class SkillService extends BaseService {
    
    private $skillDao;
    
    /**
     * @ignore
     */
    public function getSkillDao() {
        
        if (!($this->skillDao instanceof SkillDao)) {
            $this->skillDao = new SkillDao();
        }
        
        return $this->skillDao;
    }

    /**
     * @ignore
     */
    public function setSkillDao($skillDao) {
        $this->skillDao = $skillDao;
    }
    
    /**
     * Saves a skill
     * 
     * Can be used for a new record or updating.
     * 
     * @version 2.6.12 
     * @param Skill $skill 
     * @return NULL Doesn't return a value
     */
    public function saveSkill(Skill $skill) {        
        $this->getSkillDao()->saveSkill($skill);        
    }
    
    /**
     * Retrieves a skill by ID
     * 
     * @version 2.6.12 
     * @param int $id 
     * @return Skill An instance of Skill or NULL
     */    
    public function getSkillById($id) {
        return $this->getSkillDao()->getSkillById($id);
    }
    
    /**
     * Retrieves a skill by name
     * 
     * Case insensitive
     * 
     * @version 2.6.12 
     * @param string $name 
     * @return Skill An instance of Skill or false
     */    
    public function getSkillByName($name) {
        return $this->getSkillDao()->getSkillByName($name);
    }    
  
    /**
     * Retrieves all skills ordered by name
     * 
     * @version 2.6.12 
     * @return Doctrine_Collection A doctrine collection of Skill objects 
     */        
    public function getSkillList() {
        return $this->getSkillDao()->getSkillList();
    }
    
    /**
     * Deletes skills
     * 
     * @version 2.6.12 
     * @param array $toDeleteIds An array of IDs to be deleted
     * @return int Number of records deleted
     */    
    public function deleteSkills($toDeleteIds) {
        return $this->getSkillDao()->deleteSkills($toDeleteIds);
    }

    /**
     * Checks whether the given skill name exists
     * 
     * Case insensitive
     * 
     * @version 2.6.12 
     * @param string $skillName Skill name that needs to be checked
     * @return boolean
     */    
    public function isExistingSkillName($skillName) {
        return $this->getSkillDao()->isExistingSkillName($skillName);
    }
    

}