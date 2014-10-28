<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SelfReviewerGroup
 *
 * @author nadeera
 */
class SelfReviewerGroup extends PluginReviewerGroup {

    /**
     *
     * @return int 
     */
    public function getId() {
        return 2;
    }

    public function __construct() {
        
    }

    public static function getInstance(){
        return new SelfReviewerGroup();
    }

}