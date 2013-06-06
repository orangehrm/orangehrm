<?php

/**
 * Description of PluginFormMergeManager
 *
 * @author samantha
 */
class PluginFormMergeManager {
    
    private static $instance;
    private $formExtensions = array();
    
    /**
     *
     * @return PluginQueryExtensionManager
     */
    public static function instance() {
        if (!(self::$instance instanceof PluginFormMergeManager)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     *
     * @param string $actionName
     * @param string $formName
     * @return array 
     */
    public function getFormExtensions($actionName = null, $formName = null) {
        if (empty($actionName)) {
            return $this->formExtensions;
        } else {
            if (array_key_exists($actionName, $this->formExtensions)) {
                if (empty($formName)) {
                    return $this->formExtensions[$actionName];
                } else {
                    if (array_key_exists($formName, $this->formExtensions[$actionName])) {
                        return $this->formExtensions[$actionName][$formName];
                    } else {
                        return array();
                    }
                }
            } else {
                return array();
            }
        }
    }
    
     /**
     *
     * @param array $queryExtensions 
     */
    public function setFormExtensions(array $formExtensions) {
        $this->formExtensions = $formExtensions;
    }

    private function __construct() {
        $this->_init();
    }
    
    private function _init() {

        $pluginsPath = sfConfig::get('sf_plugins_dir');
        $directoryIterator = new DirectoryIterator($pluginsPath);
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                
                $pluginName = $fileInfo->getFilename();
                $configuraitonPath = $pluginsPath . '/' . $pluginName . '/config/form_extensions.yml';
                
                if (is_file($configuraitonPath)) {
                    $configuraiton = sfYaml::load($configuraitonPath);
                    
                    if (!is_array($configuraiton)) {
                        continue;
                    }
                    
                    foreach ($configuraiton as $component => $configuraitonForComponent) {
                        if (!isset($this->formExtensions[$component])) {
                            $this->formExtensions[$component] = array();
                        }
                        
                        foreach ($configuraitonForComponent as $property => $value) {
                            if (!isset($this->formExtensions[$component][$property])) {
                                $this->formExtensions[$component][$property] = array();
                            }
                            
                            if (is_array($value)) {
                                foreach ($value as $k => $v) {
                                    if (isset($this->formExtensions[$component][$property][$k])) {
                                        $this->formExtensions[$component][$property][$k] = array_merge($this->formExtensions[$component][$property][$k], $v);
                                    } else {
                                        $this->formExtensions[$component][$property][$k] = $v;
                                    }
                                }
                            } else {
                                $this->formExtensions[$component][$property][] = $value;
                            }
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Merge Plugin defined forms to base form 
     * @param type $baseForm
     * @param type $actionName
     * @param type $formName 
     */
    public function mergeForms( $baseForm , $actionName = null, $formName = null ){
        $embedForms     =   $this->getFormExtensions($actionName,$formName);
            
        foreach( $embedForms as $embedForm){
            
            $formClass = new $embedForm[0]['name'];
            $baseForm->mergeForm( $formClass);
        }
        return $baseForm;
    }
    
    /**
     * Save Plugin defined forms getting base form requests
     * @param type $baseForm
     * @param type $actionName
     * @param type $formName 
     */
    public function saveMergeForms( $baseForm , $actionName = null, $formName = null ){
        $mergeFormsParams     =   $this->getFormExtensions($actionName,$formName);
            
        foreach( $mergeFormsParams as $mergeFormParam){

            $mergeForm = new $mergeFormParam[0]['name'];
//            $mergeFormsParams->save( $baseForm );
            $mergeForm->save( $baseForm );            
        }
    }

}