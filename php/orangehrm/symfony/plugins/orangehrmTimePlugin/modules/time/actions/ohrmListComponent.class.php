<?php

class ohrmListComponent extends sfComponent {

    private static $configurationFactory;
    private static $listData;
    private static $headerPartial;

    public function execute($request) {
        $this->setTemplateVariables();

        $this->params = array();
        
        foreach( $this->getVarHolder()->getAll() as $param => $val) {
                $this->params[$param] = $val;
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
        $this->className = self::$configurationFactory->getClassName();
        $this->partial = self::$headerPartial;
        
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

    protected function getDefinitions() {
        $className = self::$configurationFactory->getClassName();

        $definitions = sfYaml::load(sfConfig::get('sf_root_dir') . '/plugins/orangehrmTimePlugin/config/list_component.yml');
        $definitionParams = array_key_exists($className, $definitions) ? $definitions[$className] : $definitions['Default'];
        return $definitionParams;
    }

    protected function getDefinitionsFromPlugins() {
        $definitionsFromPlugins = sfYaml::load(sfConfig::get('sf_root_dir') . '/plugins/orangehrmTimePlugin/config/configurations_by_plugins.yml');
        return $definitionsFromPlugins['ohrmList'];
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
        $calls = $definitionsFromPlugins['calls'];
        
        if (!empty($calls)) {
            foreach ($calls as $subjectClass => $methodCalls) {
                foreach ($methodCalls as $staticMethod => $param) {
                    forward_static_call(array($subjectClass, $staticMethod), $param);
                }
            }
        }
    }

}
