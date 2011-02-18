<?php
/**
 * ProjectDao class to have CRUD operation with datasource
 *
 * @author Sujith T
 */
class ProjectDao extends BaseDao {

   /**
    * Retrieve Project List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getProjectList($orderField = 'project_id', $orderBy = 'ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('Project')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saves Project. This has to be updated to use Project entity class
    * @param Project $project
    * @returns Project
    * @throws DaoException, DataDuplicationException
    */
   public function saveProject(Project $project) {
      try {
         $q = Doctrine_Query::create()
             ->from('Project p')
             ->where('p.name = ?', $project->getName())
             ->andWhere('p.customer_id = ?', $project->getCustomerId());

         if (trim($project->getProjectId()) != "") {
            $q->andWhere('p.project_id <> ?', $project->getProjectId()) ;
         }

         if ($q->count() > 0) {
            throw new DataDuplicationException("Project already exists, cannot be saved");
         }

         if( $project->getProjectId() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($project);
            $project->setProjectId( $idGenService->getNextID() );
         }

         $project->save();
         return $project;

      } catch(Doctrine_Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve Project by Id
    * @param int $id
    * @returns Project
    * @throws DaoException
    */
   public function readProject($id) {
      try {
         $q = Doctrine_Query::create()
             ->from('Project u')
             ->where("project_id = ?", $id);

         return $q->fetchOne();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Deletes Projects
    * @param array() $projectList
    * @returns Project
    * @throws DaoException
    */
   public function deleteProject($projectList = array()) {
      try {
         if(is_array($projectList )) {
            $q = Doctrine_Query::create()
               ->delete('Project')
               ->whereIn('project_id', $projectList);

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
            return false;
         }
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search Projects
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function searchProject($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create( )
            ->from('Project')
            ->where("$searchMode = ?",trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Returns ProjectAdmin for a given project
    * @param String $projectId
    * @returns ProjectAdmin
    * @throws DaoException
    */
   public function getProjectAdmin($projectId) {
      try {
         $q = 	Doctrine_Query::create( )
            ->from('ProjectAdmin pa')
            ->leftJoin('pa.Employee emp')
            ->where("pa.project_id = ?", $projectId);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saves Project Admin
    * @param String $projectId
    * @param String $empId
    * @returns boolean
    * @throws DaoException
    */
   public function saveProjectAdmin($projectId, $empId) {
      try {
         if(!$this->isExistingProjectAdmin($projectId , $empId)) {
            $projectAdmin	=	new ProjectAdmin();
            $projectAdmin->setProjectId( $projectId );
            $projectAdmin->setEmpNumber( $empId );

            $projectAdmin->save();
            return true;
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Project Admin
    * @param String $projectId
    * @param array() $projecAdmintList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteProjectAdmin($projectId, $projecAdmintList = array()) {
      try {
         if(is_array($projecAdmintList)) {
            $q = Doctrine_Query::create()
                ->delete('ProjectAdmin')
                ->where("project_id = ?", $projectId)
                ->whereIn('emp_number', $projecAdmintList);

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
            return false;
         }
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Checking the existance of Project admin
    * @param String $projectId
    * @param String $empId
    * @returns boolean
    * @throws DaoException
    */
   public function isExistingProjectAdmin($projectId , $empId) {
      try {
         $q = 	Doctrine_Query::create()
               ->from('ProjectAdmin pa')
               ->where("pa.project_id = ?", $projectId)
               ->andWhere("pa.emp_number =?", $empId);

         if($q->count() > 0) {
            return true ;
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve project activity by projectId
    * @param String $projectId
    * @returns ProjectActivity
    * @throws DaoException
    */
   public function getProjectActivity($projectId) {
      try {
         $q = 	Doctrine_Query::create()
             ->from('ProjectActivity pa')
             ->where("pa.project_id = ?", $projectId);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save ProjectActivity
    * @param String $projectId
    * @param String $activity
    * @returns boolean
    * @throws DaoException
    */
   public function saveProjectActivity($projectId , $activity) {
      try {
         $projectActivity	=	new ProjectActivity();
         $idGenService	=	new IDGeneratorService();
         $idGenService->setEntity($projectActivity);
         $projectActivity->setActivityId( $idGenService->getNextID() );
         $projectActivity->setProjectId( $projectId );
         $projectActivity->setName( $activity );
         $projectActivity->save();

         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete ProjectActivity
    * @param array() $activityList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteProjectActivity($activityList = array()) {
      try {
         if(is_array($activityList)) {
            $q = Doctrine_Query::create()
               ->delete('ProjectActivity')
               ->whereIn('activity_id', $activityList);

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
            return false;
         }
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
}
?>
