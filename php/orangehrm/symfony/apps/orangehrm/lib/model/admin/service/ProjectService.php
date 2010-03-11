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
class ProjectService extends BaseService {
	
   /**
     * Get NalityList List
     * @return NalityList 
     */
    public function getProjectList( $orderField='project_id',$orderBy='ASC' )
    {
    	try
        {
	    	$q = Doctrine_Query::create()
			    ->from('Project')
			    ->orderBy($orderField.' '.$orderBy);
			
			$projectList = $q->execute();
			   
			return  $projectList ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    } 
    
   /**
     * Save Nationality
     * @param Nationality $nationality 
     * @return void
     */
    public function saveProject(Project $project)
    {
    	try
        {
        	if( $project->getProjectId()=='')
        	{
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($project);
				$project->setProjectId( $idGenService->getNextID() );
        	}
        	$project->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
	/**
     * Read Customer fields
     * @param $customFieldList
     * @return void
     */
    public function readProject( $id )
    {
   	 	try
        {
        	$q = Doctrine_Query::create()
				    ->from('Project u')
				     ->where("project_id = ?",$id);
				
			$project = $q->fetchOne();
        	
	    	return $project;
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
   /**
     * Delete Nationality
     * @param $nationalityList
     * @return unknown_type
     */
    public function deleteProject( $projectList )
    {
   	 	try
        {
	    	if(is_array($projectList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('Project')
					    ->whereIn('project_id', $projectList  );
	
					   
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Search Nationality
     * @return unknown_type
     */
  	public function searchProject( $searchMode, $searchValue )
    {
    	try
        {
	    	$searchValue	=	trim($searchValue);
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('Project') 
				    			 ->where("$searchMode = ?",$searchValue);
				    
			$projectList = $q->execute();
			
			return $projectList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
  
    /**
     * Get the project admin
     * @param Project $project
     * @return unknown_type
     */
    public function getProjectAdmin( Project $project )
    {
    	try
        {
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('ProjectAdmin pa')
				   				 ->leftJoin('pa.Employee emp') 
				    			 ->where("pa.project_id = '".$project->getProjectId()."'");
				    
			
			$projectAdminList = $q->execute();
			
			return $projectAdminList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param $project
     * @return unknown_type
     */
     public function saveProjectAdmin( $projectId , $empId )
     {
     	try
        {
        	if(!$this->isExistingProjectAdmin( $projectId , $empId))
        	{
	        	$projectAdmin	=	new ProjectAdmin();
	        	$projectAdmin->setProjectId( $projectId );
	        	$projectAdmin->setEmpNumber( $empId );
	
	        	$projectAdmin->save();
        	}else
        		return false;
        	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
     }
     
  	/**
     * Delete Project Admin
     * @param $nationalityList
     * @return unknown_type
     */
    public function deleteProjectAdmin( $projectId, $projecAdmintList )
    {
   	 	try
        {
	    	if(is_array($projecAdmintList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('ProjectAdmin')
					    ->where("project_id='".$projectId."'")
					    ->whereIn('emp_number', $projecAdmintList  );
	
				//print($q->getSql());
				
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
     /**
     * Is Existing project Admin
     * @param $project
     * @return unknown_type
     */
     public function isExistingProjectAdmin( $projectId , $empId )
     {
     		$q 				= 	Doctrine_Query::create( )
				   				 ->from('ProjectAdmin pa')
				    			 ->where("pa.project_id = '".$projectId."' AND pa.emp_number='".$empId."'");
				    
			if($q->count()>0)
				return true ;
			else
				return false;	
     }
     
 	/**
     * Get the project admin
     * @param Project $project
     * @return unknown_type
     */
    public function getProjectActivity( $projectId )
    {
    	try
        {
        	$q 				= 	Doctrine_Query::create( )
				   				 ->from('ProjectActivity pa')
				    			 ->where("pa.project_id = '".$projectId."'");
				    
			
			$projectActivityList = $q->execute();
			
			return $projectActivityList;
			   
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
	/**
     * 
     * @param $project
     * @return unknown_type
     */
     public function saveProjectActivity( $projectId , $activity )
     {
     	try
        {
        	$projectActivity	=	new ProjectActivity();
        	$idGenService	=	new IDGeneratorService();
			$idGenService->setEntity($projectActivity);
			$projectActivity->setActivityId( $idGenService->getNextID() );
        	$projectActivity->setProjectId( $projectId );
        	$projectActivity->setName( $activity );

        	$projectActivity->save();
        	
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
     }
     
  	/**
     * Delete Project Admin
     * @param $nationalityList
     * @return unknown_type
     */
    public function deleteProjectActivity( $activityList )
    {
   	 	try
        {
	    	if(is_array($activityList ))
	    	{
	        	$q = Doctrine_Query::create()
					    ->delete('ProjectActivity')
					    ->whereIn('activity_id', $activityList  );
	
				//print($q->getSql());
				
				$numDeleted = $q->execute();
	    	}
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
 
    
}