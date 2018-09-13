<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 7/9/18
 * Time: 2:46 PM
 */
class GetAllEmployeeRecordsService
{
    public function getAllEmployeeRecords($empNumber)
    {
//        $a = new ExtractDao();
//        var_dump('asdasdasdasdasdasd');
//        var_dump($a->extractDataFromEmployeeNum(2, 'EmpPicture'));
//        die;
        $connection = Doctrine_Manager::getInstance()->getCurrentConnection();
        try {
            $connection->beginTransaction();
            $purgeableEntities = $this->getPurgeableEntities();

            $employee = $this->getEmployeeService()->getEmployee($empNumber);
            $dataArray = array();

            foreach ($purgeableEntities as $extractStrategy => $extractEntityArray) {
                $strategy = $this->getExtractStrategy($extractStrategy);
//                var_dump($extractStrategy);

                foreach ($extractEntityArray as $extractionentity) {
//                    var_dump($extractionentity, $empNumber, $strategy instanceof EmpNumberStrategy);
                    $b = $strategy->extractData($empNumber,$extractionentity);
                    var_dump($b,'asdadad');

//                    var_dump($strategy->extractDataFromEmployeeNum($empNumber, $extractionentity));
//                    array_push($dataArray, $strategy->extractData($empNumber, $extractionentity));
                }
            }
            die;
            $employee->setPurgedAt(date('Y-m-d H:i:s'));
            $this->saveEntity($employee);
            $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
            $connection->commit();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $connection->rollback();
            Logger::getLogger('maintenance')->error($e->getCode() . ' - ' . $e->getMessage(), $e);
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getPurgeableEntities()
    {
        if (!isset($this->purgeableEntities)) {
            $this->purgeableEntities = sfYaml::load(realpath(dirname(__FILE__) . '/../../config/extraction_stategies.yml'));
        }
        return $this->purgeableEntities;
    }

    public function getEmployeeService()
    {
        if (!isset($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    public function getExtractDao()
    {
        if (!isset($this->getExtractDao)) {
            $this->getExtractDao = new ExtractDao();
        }
        return $this->getExtractDao;
    }

    public function getExtractStrategy($strategy)
    {
        return new $strategy();
    }

}