<?php

/**
 * BaseCandidateHistory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int               $id                                  Type: integer(13), primary key
 * @property int               $candidateId                         Type: integer(13)
 * @property int               $vacancyId                           Type: integer(13)
 * @property string            $candidateVacancyName                Type: string(255)
 * @property int               $interviewId                         Type: integer(13)
 * @property int               $action                              Type: integer(4)
 * @property int               $performedBy                         Type: integer(13)
 * @property string            $performedDate                       Type: datetime, Date and time in ISO-8601 format (YYYY-MM-DD HH:MI)
 * @property string            $note                                Type: string(2147483647)
 * @property string            $interviewers                        Type: string(255)
 * @property JobCandidate      $JobCandidate                        
 * @property JobVacancy        $JobVacancy                          
 * @property Employee          $Employee                            
 * @property JobInterview      $JobInterview                        
 *  
 * @method int                 getId()                              Type: integer(13), primary key
 * @method int                 getCandidateid()                     Type: integer(13)
 * @method int                 getVacancyid()                       Type: integer(13)
 * @method string              getCandidatevacancyname()            Type: string(255)
 * @method int                 getInterviewid()                     Type: integer(13)
 * @method int                 getAction()                          Type: integer(4)
 * @method int                 getPerformedby()                     Type: integer(13)
 * @method string              getPerformeddate()                   Type: datetime, Date and time in ISO-8601 format (YYYY-MM-DD HH:MI)
 * @method string              getNote()                            Type: string(2147483647)
 * @method string              getInterviewers()                    Type: string(255)
 * @method JobCandidate        getJobCandidate()                    
 * @method JobVacancy          getJobVacancy()                      
 * @method Employee            getEmployee()                        
 * @method JobInterview        getJobInterview()                    
 *  
 * @method CandidateHistory    setId(int $val)                      Type: integer(13), primary key
 * @method CandidateHistory    setCandidateid(int $val)             Type: integer(13)
 * @method CandidateHistory    setVacancyid(int $val)               Type: integer(13)
 * @method CandidateHistory    setCandidatevacancyname(string $val) Type: string(255)
 * @method CandidateHistory    setInterviewid(int $val)             Type: integer(13)
 * @method CandidateHistory    setAction(int $val)                  Type: integer(4)
 * @method CandidateHistory    setPerformedby(int $val)             Type: integer(13)
 * @method CandidateHistory    setPerformeddate(string $val)        Type: datetime, Date and time in ISO-8601 format (YYYY-MM-DD HH:MI)
 * @method CandidateHistory    setNote(string $val)                 Type: string(2147483647)
 * @method CandidateHistory    setInterviewers(string $val)         Type: string(255)
 * @method CandidateHistory    setJobCandidate(JobCandidate $val)   
 * @method CandidateHistory    setJobVacancy(JobVacancy $val)       
 * @method CandidateHistory    setEmployee(Employee $val)           
 * @method CandidateHistory    setJobInterview(JobInterview $val)   
 *  
 * @package    orangehrm
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCandidateHistory extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_job_candidate_history');
        $this->hasColumn('id', 'integer', 13, array(
             'type' => 'integer',
             'primary' => true,
             'length' => 13,
             ));
        $this->hasColumn('candidate_id as candidateId', 'integer', 13, array(
             'type' => 'integer',
             'length' => 13,
             ));
        $this->hasColumn('vacancy_id as vacancyId', 'integer', 13, array(
             'type' => 'integer',
             'length' => 13,
             ));
        $this->hasColumn('candidate_vacancy_name as candidateVacancyName', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('interview_id as interviewId', 'integer', 13, array(
             'type' => 'integer',
             'length' => 13,
             ));
        $this->hasColumn('action', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('performed_by as performedBy', 'integer', 13, array(
             'type' => 'integer',
             'length' => 13,
             ));
        $this->hasColumn('performed_date as performedDate', 'datetime', null, array(
             'type' => 'datetime',
             ));
        $this->hasColumn('note', 'string', 2147483647, array(
             'type' => 'string',
             'length' => 2147483647,
             ));
        $this->hasColumn('interviewers', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('JobCandidate', array(
             'local' => 'candidateId',
             'foreign' => 'id'));

        $this->hasOne('JobVacancy', array(
             'local' => 'vacancyId',
             'foreign' => 'id'));

        $this->hasOne('Employee', array(
             'local' => 'performedBy',
             'foreign' => 'empNumber'));

        $this->hasOne('JobInterview', array(
             'local' => 'interviewId',
             'foreign' => 'id'));
    }
}