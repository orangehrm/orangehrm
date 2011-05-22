<?php

class ohrmListComponent extends sfComponent {
    
    private static $configurationFactory;
    private static $listData;

    public function execute($request) {
        $className = self::$configurationFactory->getClassName();

        $definitions = sfYaml::load(sfConfig::get('sf_root_dir') . '/plugins/orangehrmCorePlugin/config/list_component.yml');      
        $params = array_key_exists($className, $definitions) ? $definitions[$className] : $definitions['Default'];

        foreach ($params as $key => $value) {
            $this->setVar($key, $value);
        }

        $recordsLimit = sfConfig::get('app_items_per_page');
        $pageNo = $request->getParameter('pageNo', 1);

        $numberOfRecords = (self::$listData instanceof Doctrine_Collection) ? self::$listData->count() : count(self::$listData); // TODO: Remove the dependancy of ORM here; Use a Countable interface and a Iterator interface
        
        $pager = new SimplePager($this->className, $recordsLimit);
        $pager->setPage($pageNo);
        $pager->setNumResults($numberOfRecords);
        $pager->init();
        
        $offset = $pager->getOffset();
        $offset = empty($offset) ? 0 : $offset;

        $this->offset = $offset;
        $this->pager = $pager;
        $this->recordLimit = $recordsLimit;
        
        $this->currentSortField = $request->getParameter('sortField', '');
        $this->currentSortOrder = $request->getParameter('sortOrder', '');
        
        $this->columns = self::$configurationFactory->getHeaders();
        $this->data = self::$listData;
        $this->className = $className;
    }
    
    public static function setConfigurationFactory(ListConfigurationFactory $configurationFactory) {
        self::$configurationFactory = $configurationFactory;
    }
    
    public static function setListData($data) {
        self::$listData = $data;
    }

}
