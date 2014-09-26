<?php

class ohrmListComponent extends sfComponent {

    protected static $configurationFactory;
    protected static $listData;
    protected static $itemsPerPage;
    protected static $headerPartial;
    protected static $footerPartial;
    protected static $numberOfRecords;
    protected static $definitionsPath;
    protected static $activePlugin;
    protected static $listForm ;

    public static $pageNumber = 0;

    /**
     *
     * @param sfRequest $request 
     */
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
        $this->footerPartial = self::$footerPartial;
        $this->listForm = ( empty( self::$listForm) )? new DefaultListForm() :  self::$listForm ;
          
                
        $this->applyRuntimeDefinitions();

        $this->makePluginCalls();
    }

    /**
     *
     * @param ListConfigurationFactory $configurationFactory 
     */
    public static function setConfigurationFactory(ListConfigurationFactory $configurationFactory) {
        self::$configurationFactory = $configurationFactory;
    }

    /**
     *
     * @return mixed
     */
    public static function getListData() {
        return self::$listData;
    }
    
    /**
     *
     * @param mixed $data 
     */
    public static function setListData($data) {
        self::$listData = $data;
    }

     /**
     *
     * @return mixed
     */
    public static function getListForm() {
        
        
        return self::$listForm;
    }
    
    /**
     *
     * @param mixed $data 
     */
    public static function setListForm($form) {
        self::$listForm = $form;
    }
    
    /**
     *
     * @param string $partial 
     */
    public static function setHeaderPartial($partial) {
        self::$headerPartial = $partial;
    }

    /**
     *
     * @param string $partial 
     */
    public static function setFooterPartial($partial) {
        self::$footerPartial = $partial;
    }    
    /**
     *
     * @param int $items 
     */
    public static function setItemsPerPage($items) {
        self::$itemsPerPage = $items;
    }

    /**
     *
     * @param int $count 
     */
    public static function setNumberOfRecords($count) {
        self::$numberOfRecords = $count;
    }
    
    /**
     *
     * @param int $pageNumber 
     */
    public static function setPageNumber ($pageNumber) {
        self::$pageNumber = $pageNumber;
    }
    
    /**
     *
     * @return string
     */
    public static function getDefinitionsPath() {
        return self::$definitionsPath;
    }
    
    /**
     *
     * @param string $path 
     */
    public static function setDefinitionsPath($path) {
        self::$definitionsPath = $path;
    }
    
    /**
     *
     * @return string
     */
    public static function getActivePlugin() {
        return self::$activePlugin;
    }
    
    /**
     *
     * @param string $pluginName 
     */
    public static function setActivePlugin($pluginName) {
        self::$activePlugin = $pluginName;
    }

    /**
     *
     * @return array
     */
    protected function getDefinitions() {
        $className = self::$configurationFactory->getClassName();

        $definitions = $this->loadDefinitions();
        $definitionParams = array_key_exists($className, $definitions) ? $definitions[$className] : $definitions['Default'];
        return $definitionParams;
    }
    
    /**
     *
     * @return array
     */
    protected function loadDefinitions() {
        
        if (empty(self::$definitionsPath)) {
            if (empty(self::$activePlugin)) {
                self::$definitionsPath = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/config/list_component.yml';
            } else {
                self::$definitionsPath = sfConfig::get('sf_plugins_dir') . '/' . self::$activePlugin . '/config/list_component.yml';;    
            }
        }
        
        return sfYaml::load(self::$definitionsPath);
    }

    /**
     *
     * @return array 
     */
    protected function getDefinitionsFromPlugins() {
        return PluginConfigurationManager::instance()->getExternalConfigurations('ohrmListComponent');
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    protected function applyRuntimeDefinitions() {
        $runtimeDefinitions = self::$configurationFactory->getRuntimeDefinitions();
        foreach ($runtimeDefinitions as $key => $value) {
            $this->setVar($key, $value);
        }
    }

}
