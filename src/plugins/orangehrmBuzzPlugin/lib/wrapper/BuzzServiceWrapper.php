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
 * Description of BuzzServiceWrapper
 *
 * @author nirmal
 */
class BuzzServiceWrapper implements WebServiceWrapper {

    protected $buzzWebServiceHelper;

    const DEFAULT_SHARE_LIMIT = 10;

    public function getServiceInstance() {
        if (!$this->buzzWebServiceHelper instanceof BuzzWebServiceHelper) {
            $this->buzzWebServiceHelper = new BuzzWebServiceHelper();
        }
        return $this->buzzWebServiceHelper;
    }

    /**
     * Get Current Logged in Employee Number
     * @param type $options
     */
    public function getLoggedInEmployeeNumber() {
        return sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
    }

    /**
     *
	 * @api {get} /getLoggedInEmployee Get LoggedIn Employee
     * @apiDescription Get LoggedIn Employee
     * @apiVersion 0.1.0
     * @apiName getLoggedInEmployee
     * @apiGroup BUZZ
     * @apiSuccess {Array} Employee Logged in employee
     */
    public function getLoggedInEmployee() {
        return $this->getServiceInstance()->getLoggedInEmployee();
    }

    /**
     *
	 * @api {get} /getLatestBuzzShares/recentShareId/:recentShareId Get Latest Buzz Shares
     * @apiDescription Get Latest Buzz Shares
     * @apiVersion 0.1.0
     * @apiName getLatestBuzzShares
     * @apiGroup BUZZ
     * @apiSuccess {Array} Shares Get latest shares
     */
    public function getLatestBuzzShares($recentShareId) {
        return $this->getServiceInstance()->getLatestBuzzShares($recentShareId);
    }

    /**
     *
	 * @api {get} /getBuzzShares/limit/:limit Get shares
     * @apiDescription Get shares
     * @apiVersion 0.1.0
     * @apiName getBuzzShares
     * @apiGroup BUZZ
     * @apiSuccess {Array} Shares Get recent [at first load] shares default number of shares are 10
     */
    public function getBuzzShares($limit) {
        return $this->getServiceInstance()->getBuzzShares($limit);
    }


    /**
     *
	 * @api {get} /getMoreBuzzShares/lastShareId/:lastShareId/limit/:limit Get More shares
     * @apiDescription Get More shares
     * @apiVersion 0.1.0
     * @apiName getMoreBuzzShares
     * @apiGroup BUZZ
     * @apiSuccess {Array} Shares Get shares older than a given share Id
     */
    public function getMoreBuzzShares($lastShareId, $limit) {
        return $this->getServiceInstance()->getMoreBuzzShares($lastShareId, $limit);
    }


    /**
     *
	 * @api {get} /getShareAndPostDetailsByShareId/shareId/:shareId Get share by share id
     * @apiDescription Get share by share id
     * @apiVersion 0.1.0
     * @apiName getShareAndPostDetailsByShareId
     * @apiGroup BUZZ
     * @apiError shareIdIsNotValid Valid parameters are not provided
     * @apiSuccess {Array} Shares Get share and post details by share id, this will retun post details, comment and like details, etc
     */
    public function getShareAndPostDetailsByShareId($shareId) {
        if (is_null($shareId)) {
            throw new Exception("Valid parameters are not provided");
        } else {
            return $this->getServiceInstance()->getShareAndPostDetailsByShareId($shareId);
        }
    }

    /**
     *
	 * @api {get} /postContentOnFeed/contentText/:contentText/image_data/:image_data Post content on news feed
     * @apiDescription Post content on news feed
     * @apiVersion 0.1.0
     * @apiName postContentOnFeed
     * @apiGroup BUZZ
     * @apiError contentTextAndimage_dataIsNull Valid parameters are not provided
     * @apiSuccess {Array} Share Share
     */
    public function postContentOnFeed($contentText, $image_data) {
        if (is_null($contentText)) {
            throw new Exception("Valid parameters are not provided");
        } else {
            $empNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->postContentOnFeed($empNumber, $contentText, date("Y-m-d H:i:s"), $image_data);
        }
    }


    /**
     *
     * @api {get} /getBuzzImage/imageId/:imageId
     * @apiDescription Gets the image from the id
     * @apiVersion 0.1.0
     * @apiName getBuzzImage
     * @apiGroup BUZZ
     * @apiSuccess Photo
     */
    public function getBuzzImage($imageId) {
        $this->getServiceInstance()->getBuzzImage($imageId);
    }

    /**
     *
     * @api {get} /getEmployeeImage/empNumber/:empNumber
     * @apiDescription Gets the image of employee from the empNumber
     * @apiVersion 0.1.0
     * @apiName getEmployeeImage
     * @apiGroup BUZZ
     * @apiSuccess Photo
     */
    public function getEmployeeImage($empNumber) {
        $this->getServiceInstance()->getEmployeeImage($empNumber);
    }

    /**
     *
	 * @api {get} /commentOnShare/shareId/:shareId/contentText/:contentText Comment On Share
     * @apiDescription Comment On Share
     * @apiVersion 0.1.0
     * @apiName commentOnShare
     * @apiGroup BUZZ
     * @apiError shareIdAndcontentTextIsNull Valid parameters are not provided
     * @apiSuccess {Array} Comment Comment added to the share
     */
    public function commentOnShare($shareId, $contentText) {
        if (is_null($shareId && $contentText)) {
            throw new Exception("Valid parameters are not provided");
        } else {
            $empNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->commentOnShare($shareId, $empNumber, $contentText, date("Y-m-d H:i:s"));
        }
    }

    /**
     *
	 * @api {get} /likeOnShare/shareId/:shareId Like On Share
     * @apiDescription Like on a share / post
     * @apiVersion 0.1.0
     * @apiName likeOnShare
     * @apiGroup BUZZ
     * @apiError shareIdIsNull Valid parameters are not provided
     * @apiSuccess {Array} Share Share that is liked
     */
    public function likeOnShare($shareId) {
        if (is_null($shareId)) {
            throw new Exception("Valid parameters are not provided");
        } else {
            $empNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->likeOnShare($shareId, $empNumber, date("Y-m-d H:i:s"));
        }
    }

    /**
     *
	 * @api {get} /disLikeOnShare/shareId/:shareId Dislike On Share
     * @apiDescription Dislike on a share / post
     * @apiVersion 0.1.0
     * @apiName disLikeOnShare
     * @apiGroup BUZZ
     * @apiError shareIdIsNull Valid parameters are not provided
     * @apiSuccess {Array} Share Share that is disliked
     */
    public function disLikeOnShare($shareId) {
        if (is_null($shareId)) {
            throw new Exception("Valid parameters are not provided");
        } else {
            $empNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->dislikeOnShare($shareId, $empNumber, date("Y-m-d H:i:s"));
        }
    }

    /**
     *
	 * @api {get} /likeOnComment/commentId/:commentId Like on a comment
     * @apiDescription Like on a comment
     * @apiVersion 0.1.0
     * @apiName likeOnComment
     * @apiGroup BUZZ
     * @apiError commentIdIsNull Valid parameters are not provided
     * @apiSuccess {Array} Comment Comment that is liked
     */
    public function likeOnComment($commentId) {
        if (is_null($commentId)) {
            throw new Exception("Valid parameters are not provided");
        } else {
            $empNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->likeOnComment($commentId, $empNumber, date("Y-m-d H:i:s"));
        }
    }

    /**
     *
	 * @api {get} /dislikeOnComment/commentId/:commentId Dislike on a comment
     * @apiDescription Dislike on a comment
     * @apiVersion 0.1.0
     * @apiName dislikeOnComment
     * @apiGroup BUZZ
     * @apiError commentIdIsNull Valid parameters are not provided
     * @apiSuccess {Array} Comment Comment that is disliked
     */
    public function dislikeOnComment($commentId) {
        if (is_null($commentId)) {
            throw new Exception("Valid parameters are not provided");
        } else {
            $empNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->dislikeOnComment($commentId, $empNumber, date("Y-m-d H:i:s"));
        }
    }

    /**
     *
	 * @api {get} /sharePost/shareId/:shareId Share Post
     * @apiDescription Sharing a share / post
     * @apiVersion 0.1.0
     * @apiName sharePost
     * @apiGroup BUZZ
     * @apiError shareIdIsNull Valid parameters are not provided
     * @apiSuccess {Array} Share Shared Post
     */
    public function sharePost($postId, $newText){
        if (!is_null($postId) || !is_null($newText)) {
            $empNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->sharePost($postId, $empNumber, $newText);
        } else {
            throw new Exception("Valid parameters are not provided");
        }
    }
    
    /**
     *
	 * @api {get} /getBuzzForEmployee/:empNum Get buzz by employee
     * @apiDescription Get buzz by employee number
     * @apiVersion 0.1.0
     * @apiName getBuzzForEmployee
     * @apiGroup BUZZ
     * @apiError empNum Is Null Valid parameters are not provided
     * @apiSuccess {Array} Shares Get shares made by the Employee
     */
    public function getBuzzForEmployee($empNum) {
        if (!is_null($empNum)) {
            $loggedInEmpNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->getBuzzForEmployee($empNum,$loggedInEmpNumber);
        } else {
            throw new Exception("Valid parameters are not provided");
        }
    }

    /**
     *
	 * @api {post} /deleteShare/:shareId Delete Post
     * @apiDescription Delete a share
     * @apiVersion 0.1.0
     * @apiName deleteShare
     * @apiGroup BUZZ
     * @apiError shareId Is Null Valid parameters are not provided
     * @apiSuccess {Array} Share delete success state
     */
    public function deleteShare($shareId) {
        if (!is_null($shareId)) {
            $loggedInEmployeeNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->deleteShare($shareId, $loggedInEmployeeNumber);
        } else {
            throw new Exception("Valid parameters are not provided");
        }
    }

    /**
     *
	 * @api {post} /deleteComment/:commentId Delete Comment
     * @apiDescription Delete a comment
     * @apiVersion 0.1.0
     * @apiName deleteComment
     * @apiGroup BUZZ
     * @apiError commentId Is Null Valid parameters are not provided
     * @apiSuccess {Array} Share comment delete success state
     */
    public function deleteComment($commentId) {
        if (!is_null($commentId)) {
            $loggedInEmployeeNumber = $this->getLoggedInEmployeeNumber();
            return $this->getServiceInstance()->deleteCommentForShare($commentId, $loggedInEmployeeNumber);
        } else {
            throw new Exception("Valid parameters are not provided");
        }
    }

}
