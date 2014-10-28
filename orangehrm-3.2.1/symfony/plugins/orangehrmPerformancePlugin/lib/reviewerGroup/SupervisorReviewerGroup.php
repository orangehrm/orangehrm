<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SupervisorReviewerGroup
 *
 * @author nadeera
 */
class SupervisorReviewerGroup extends PluginReviewerGroup {
    
    /**
     *
     * @return int 
     */
    public function getId(){
        return 1;
    }
    
    public function __construct() {
        
    }

    public static function getInstance(){
        return new SupervisorReviewerGroup();
    }

}

?>
