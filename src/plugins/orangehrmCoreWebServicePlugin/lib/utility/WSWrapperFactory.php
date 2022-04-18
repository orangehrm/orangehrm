<?php

class WSWrapperFactory extends baseWSUtility {
    
    /**
     *
     * @param WSRequestParameters $paramObj 
     * @return WebServiceWrapper
     * @todo Complete the implementation of this method to create WS wrappers dynamically
     */
    public function create(WSRequestParameters $paramObj) {
        return null;
    }
    
    /**
     *
     * @param WSRequestParameters $paramObj 
     * @return string
     */
    protected function extractMethodName(WSRequestParameters $paramObj) {
        
    }
    
    /**
     *
     * @param WSRequestParameters $paramObj 
     * @return array
     */
    protected function extractMethodParams(WSRequestParameters $paramObj) {
        
    }
    
    /**
     *
     * @param WebServiceWrapper $service
     * @param type $methodName
     * @param array $methodParams 
     * @return mixed
     */
    protected function call(WebServiceWrapper $service, $methodName, array $methodParams) {
        
    }
}
