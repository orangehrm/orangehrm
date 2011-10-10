<?php

class ohrmListComponent extends sfComponent {

    private static $configurationFactory;
    private static $listData;
    private static $itemsPerPage;
    private static $headerPartial;
    private static $numberOfRecords;
    public static $pageNumber = 0;

    public function execute($request) {
        $this->setTemplateVariables();

        $this->params = array();
        
        foreach( $this->getVarHolder()->getAll() as $param => $val) {
                $this->params[$param] = $val;
        }

        $recordsLimit = self::$itemsPerPage;//sfConfig::get('app_items_per_page');
        $pageNo = $request->getParameter('pageNo', 1);

        if (self::$pageNumber) {
            $pageNo = self::$pageNumber;
        } else {
            $pageNo = $request->getParameter('pageNo', 1);
        }

        $numberOfRecords = self::$numberOfRecords;//replace with the count of all the records(self::$listData instanceof Doctrine_Collection) ? self::$listData->count() : count(self::$listData); // TODO: Remove the dependancy of ORM here; Use a Countable interface and a Iterator interface

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

        $this->showGroupHeaders = self::$configurationFactory->showGroupHeaders();
        $this->headerGroups = self::$configurationFactory->getHeaderGroups();
        $this->columns = self::$configurationFactory->getHeaders();
        $this->data = self::$listData;
        $this->className = self::$configurationFactory->getClassName();
        $this->partial = self::$headerPartial;
        $this->applyRuntimeDefinitions();

        $this->makePluginCalls();
    }

    public static function setConfigurationFactory(ListConfigurationFactory $configurationFactory) {
        self::$configurationFactory = $configurationFactory;
    }

    public static function setListData($data) {
        self::$listData = $data;
    }

    public static function setHeaderPartial($partial) {
        self::$headerPartial = $partial;
    }

    public static function setItemsPerPage($items) {
        self::$itemsPerPage = $items;
    }

    public static function setNumberOfRecords ($count) {
        self::$numberOfRecords = $count;
    }
    
    public static function setPageNumber ($pageNumber) {
        self::$pageNumber = $pageNumber;
    }

    protected function getDefinitions() {
        $className = self::$configurationFactory->getClassName();

        $definitions = sfYaml::load(sfConfig::get('sf_root_dir') . '/plugins/orangehrmCorePlugin/config/list_component.yml');
        $definitionParams = array_key_exists($className, $definitions) ? $definitions[$className] : $definitions['Default'];
        return $definitionParams;
    }

    protected function getDefinitionsFromPlugins() {
        return PluginConfigurationManager::instance()->getExternalConfigurations('ohrmListComponent');
    }

    protected function setTemplateVariables() {
        $definitions = $this->getDefinitions();
        $definitionsFromPlugins = $this->getDefinitionsFromPlugins();

        foreach ($definitions as $key => $value) {
            $this->setVar($key, $value);
        }

        foreach ($definitionsFromPlugins as $key => $value) {
            if ($key == 'calls') {
                continue;
            }
            $this->setVar('extra' . ucfirst($key), $value);
        }
    }

    protected function makePluginCalls() {
        $definitionsFromPlugins = $this->getDefinitionsFromPlugins();
        $calls = isset($definitionsFromPlugins['calls']) ? $definitionsFromPlugins['calls'] : '';

        if (!empty($calls)) {
            foreach ($calls as $subjectClass => $methodCalls) {
                foreach ($methodCalls as $staticMethod => $param) {
                    forward_static_call(array($subjectClass, $staticMethod), $param);
                }
            }
        }
    }

    protected function applyRuntimeDefinitions() {
        $runtimeDefinitions = self::$configurationFactory->getRuntimeDefinitions();
        foreach ($runtimeDefinitions as $key => $value) {
            $this->setVar($key, $value);
        }
    }

}
