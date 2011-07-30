<?php

class ExportDataRetriever implements Exportable {

    private $configurationFactory;
    private $dataRetrievalService;
    private $dataRetrievalMethod;
    private $dataRetrievalParams;
    private $listData = array();


    public function getConfigurationFactory() {
        return $this->configurationFactory;
    }

    public function setConfigurationFactory($configurationFactory) {
        $this->configurationFactory = $configurationFactory;
    }

    public function getDataRetrievalService() {
        return $this->dataRetrievalService;
    }

    public function setDataRetrievalService(BaseService $service) {
        $this->dataRetrievalService = $service;
    }

    public function getDataRetrievalMethod() {
        return $this->dataRetrievalMethod;
    }

    public function setDataRetrievalMethod($dataRetrievalMethod) {
        $this->dataRetrievalMethod = $dataRetrievalMethod;
    }

    public function getDataRetrievalParams() {
        return $this->dataRetrievalParams;
    }

    public function setDataRetrievalParams(array $dataRetrievalParams = array()) {
        $this->dataRetrievalParams = $dataRetrievalParams;
    }

    public function getExportHeaders() {
        $headerArray = array();
        $headers = $this->configurationFactory->getHeaders();

        foreach ($headers as $header) {
            if ($header->isExportable()) {
                $headerArray[] = __($header->getName());
            }
        }

        return $headerArray;
    }

    public function getExportData() {
        $listData = call_user_func_array(array($this->dataRetrievalService, $this->dataRetrievalMethod), $this->dataRetrievalParams);

        if ($listData instanceof Doctrine_Collection || is_array($listData)) {
            $headers = $this->configurationFactory->getHeaders();
            $dataArray = array();
            $i = 0;

            foreach ($listData as $object) {
                $dataArray[$i] = array();
                foreach ($headers as $header) {
                    if ($header->isExportable()) {
                        $elementType = $header->getElementType();
                        $properties = $header->getElementProperty();
                        $cellClass = ucfirst($header->getElementType()) . 'Cell';

                        $cell = new $cellClass;
                        $cell->setProperties($properties);
                        $cell->setDataObject($object);

                        $dataArray[$i][] = $cell->toValue();
                    }
                }
                $i++;
            }

            return $dataArray;
        } else {
            return $this->listData;
        }
    }

}
