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
class LanguageService extends BaseService {
    
    private $languageDao;
    
    /**
     * @ignore
     */
    public function getLanguageDao() {
        
        if (!($this->languageDao instanceof LanguageDao)) {
            $this->languageDao = new LanguageDao();
        }
        
        return $this->languageDao;
    }

    /**
     * @ignore
     */
    public function setLanguageDao($languageDao) {
        $this->languageDao = $languageDao;
    }
    
    /**
     * Saves a language
     * 
     * Can be used for a new record or updating.
     * 
     * @version 2.6.12 
     * @param Language $language 
     * @return NULL Doesn't return a value
     */
    public function saveLanguage(Language $language) {        
        $this->getLanguageDao()->saveLanguage($language);        
    }
    
    /**
     * Retrieves a language by ID
     * 
     * @version 2.6.12 
     * @param int $id 
     * @return Language An instance of Language or NULL
     */    
    public function getLanguageById($id) {
        return $this->getLanguageDao()->getLanguageById($id);
    }
    
    /**
     * Retrieves a language by name
     * 
     * Case insensitive
     * 
     * @version 2.6.12 
     * @param string $name 
     * @return Language An instance of Language or false
     */    
    public function getLanguageByName($name) {
        return $this->getLanguageDao()->getLanguageByName($name);
    }        
  
    /**
     * Retrieves all languages ordered by name
     * 
     * @version 2.6.12 
     * @return Doctrine_Collection A doctrine collection of Language objects 
     */        
    public function getLanguageList() {
        return $this->getLanguageDao()->getLanguageList();
    }
    
    /**
     * Deletes languages
     * 
     * @version 2.6.12 
     * @param array $toDeleteIds An array of IDs to be deleted
     * @return int Number of records deleted
     */    
    public function deleteLanguages($toDeleteIds) {
        return $this->getLanguageDao()->deleteLanguages($toDeleteIds);
    }

    /**
     * Checks whether the given language name exists
     *
     * Case insensitive
     *
     * @version 2.6.12
     * @param string $languageName Language name that needs to be checked
     * @return boolean
     */
    public function isExistingLanguageName($languageName) {
        return $this->getLanguageDao()->isExistingLanguageName($languageName);
    }
    
}