<?php

final class ListCompnentParameterHolder {

    protected $configurationFactory;
    protected $listData;
    protected $noOfRecords;
    protected $totalRecordsCount;
    protected $pageNumber;

    /**
     *
     * @return ListConfigurationFactory
     */
    public function getConfigurationFactory() {
        return $this->configurationFactory;
    }

    /**
     *
     * @param ListConfigurationFactory $configurationFactory 
     */
    public function setConfigurationFactory(ListConfigurationFactory $configurationFactory) {
        $this->configurationFactory = $configurationFactory;
    }

    /**
     *
     * @return mixed array or Doctrine_Collection
     */
    public function getListData() {
        return $this->listData;
    }

    /**
     *
     * @param mixed $listData array or Doctrine_Collection
     */
    public function setListData($listData) {
        $this->listData = $listData;
    }

    /**
     *
     * @return int
     */
    public function getNoOfRecords() {
        return $this->noOfRecords;
    }

    /**
     *
     * @param int $noOfRecords 
     */
    public function setNoOfRecords($noOfRecords) {
        $this->noOfRecords = $noOfRecords;
    }

    /**
     *
     * @return int
     */
    public function getTotalRecordsCount() {
        return $this->totalRecordsCount;
    }

    /**
     *
     * @param int $totalRecordsCount 
     */
    public function setTotalRecordsCount($totalRecordsCount) {
        $this->totalRecordsCount = $totalRecordsCount;
    }

    /**
     *
     * @return int
     */
    public function getPageNumber() {
        return $this->pageNumber;
    }

    /**
     *
     * @param int $pageNumber 
     */
    public function setPageNumber($pageNumber) {
        $this->pageNumber = $pageNumber;
    }
    
    /**
     *
     * @param array $values 
     */
    public function populateByArray(array $values) {
        foreach ($values as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            } else {
                throw new Exception('The property ' . $property . ' does not exist');
            }
        }
    }

}