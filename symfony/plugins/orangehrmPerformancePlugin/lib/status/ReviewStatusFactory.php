<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReviewStatusFactory
 *
 * @author nadeera
 */
class ReviewStatusFactory {

    /**
     *
     * @param type $id
     * @return \ReviewStatusInactive|\ReviewStatusActivated|\ReviewStatusInProgress|\ReviewStatusApproved 
     */
    public function getStatus($id = null) {

        switch ($id) {
            case 1:
            case 'inactive':
                return new ReviewStatusInactive();
                break;
            case 2:
            case 'activated':
                return new ReviewStatusActivated();
                break;
            case 3:
            case 'inProgress':
                return new ReviewStatusInProgress ();
                break;
            case 4:
            case 'approved':
                return new ReviewStatusApproved();
                break;
            default:                
                return new ReviewStatusInactive();
                break;
        }
    }
    
    /**
     *
     * @return ReviewStatusFactory 
     */
    public static function getInstance(){
        return new ReviewStatusFactory();
    }

}

