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

/**
 * Description of CompanyService
 *
 * @author orange
 */
class SkillService extends BaseService {
	
   /**
     * Get Skill List
     * @return Skill 
     */
    public function getSkillList( $orderField='skill_code',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('Skill')
			    ->orderBy($orderField.' '.$orderBy);
			
			$skillList = $q->execute();
			   
			return  $skillList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
   /**
     * Save Skill
     * @param Skill $skill
     * @return void
     */
    public function saveSkill(Skill $skill)
    {
    	try
        {
        	if( $skill->getSkillCode() == '')
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($skill);
				$skill->setSkillCode( $idGenService->getNextID() );
        	}
        	$skill->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete Skill
     * @param $skillList
     * @return unknown_type
     */
    public function deleteSkill( $skillList )
    {
   	 	try
        {
	    	if(is_array( $skillList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('Skill')
					    ->whereIn('skill_code', $skillList  );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Search Skill
     * @param $saleryGradeList
     * @return unknown_type
     */
  	public function searchSkill( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('Skill') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$skillList = $q->execute();
			
			return $skillList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Read Skill
     * @return Skill
     */
    public function readSkill( $id )
    {
   	 	try
        {
	    	$skill = Doctrine::getTable('Skill')->find($id);
	    	return $skill;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
    
   /**
     * Get Language List
     * @return Skill 
     */
    public function getLanguageList( $orderField='lang_code',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('Language')
			    ->orderBy($orderField.' '.$orderBy);
			
			$languageList = $q->execute();
			   
			return  $languageList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
   /**
     * Save Language
     * @param Language $language
     * @return void
     */
    public function saveLanguage(Language $language)
    {
    	try
        {
        	if( $language->getLangCode() == '')
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($language);
				$language->setLangCode( $idGenService->getNextID() );
        	}
        	$language->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete Language
     * @param $skillList
     * @return unknown_type
     */
    public function deleteLanguage( $languageList )
    {
   	 	try
        {
	    	if(is_array($languageList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('Language')
					    ->whereIn('lang_code', $languageList  );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Search Language
     * @return unknown_type
     */
  	public function searchLanguage( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('Language') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$languageList = $q->execute();
			
			return $languageList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Read Language
     * @return Language
     */
    public function readLanguagee( $id )
    {
   	 	try
        {
	    	$language = Doctrine::getTable('Language')->find($id);
	    	return $language;
	    	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
     
        }
    } 
}