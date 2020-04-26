<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

/**
 * Description of mostLikedSharesComponent
 *
 * @author aruna
 */
class mostLikedSharesComponent extends sfComponent {

    protected $buzzService;
    /**
     * return buzz service
     * @return buzzService
     */
    protected function getBuzzService() {
        if (!($this->buzzService instanceof BuzzService)) {
            $this->buzzService = new BuzzService();
        }
        return $this->buzzService;
    }
     
    /**
     * 
     * @return BuzzConfigService
     */
    private function getBuzzConfigService() {
        if (!$this->buzzConfigService) {
            $this->buzzConfigService = new BuzzConfigService();
        }
        return $this->buzzConfigService;
    }
    
    public function execute($request) {
        $this->buzzService= $this->getBuzzService();
        $mostLikeShareCount= $this->getBuzzConfigService()->getMostLikeShareCount();
        $mostLikePostCount= $this->getBuzzConfigService()->getMostLikePostCount();
        $mostLikedShares = $this->buzzService->getMostLikedShares($mostLikeShareCount);
        $mostCommentedShares = $this->buzzService->getMostCommentedShares($mostLikePostCount);

        $this->result_ml_shares = array();
        $this->result_ml_shares_like_count = array();
        $this->result_mc_shares = array();
        $this->result_mc_shares_comment_count = array();

        $this->setMostLikeShares($mostLikedShares);
        $this->setMostCommentedShares($mostCommentedShares);
    }
    
    /**
     * set most like shares for view
     * @param type $mostLikedShares
     */
    private function setMostLikeShares($mostLikedShares) {
        foreach ($mostLikedShares as $share) {
            $s = $this->buzzService->getShareById($share['share_id']);
            $n = $share['no_of_likes'];
            array_push($this->result_ml_shares, $s);
            array_push($this->result_ml_shares_like_count, $n);
        }
    }
    
    /**
     * set most commented post for view
     * @param type $mostCommentedShares
     */
    private function setMostCommentedShares($mostCommentedShares) {
        foreach ($mostCommentedShares as $share) {
            $s = $this->buzzService->getShareById($share['share_id']);
            $n = $share['no_of_comments'];
            array_push($this->result_mc_shares, $s);
            array_push($this->result_mc_shares_comment_count, $n);
        }
    }
   

}
