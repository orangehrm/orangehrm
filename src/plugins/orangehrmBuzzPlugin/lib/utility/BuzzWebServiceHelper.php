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
 * Description of BuzzWebServiceHelperUtility
 *
 * @author nirmal
 * Modified: ridwan [17th March 2015]
 */
class BuzzWebServiceHelper {

    protected $buzzService;
    protected $buzzObjectBuilder;

    const DEFAULT_SHARE_LIMIT = 10;

    /**
     * Get Buzz Service
     * @return BuzzService
     */
    public function getBuzzService() {
        if (!$this->buzzService instanceof BuzzService) {
            $this->buzzService = new BuzzService();
        }
        return $this->buzzService;
    }

    /**
     * Set Buzz Service
     * @param BuzzService $buzzService
     */
    public function setBuzzService(BuzzService $buzzService) {
        $this->buzzService = $buzzService;
    }

    /**
     * Get BuzzObjectBuilder
     * @return BuzzObjectBuilder
     */
    public function getBuzzObjectBuilder() {
        if (!$this->buzzObjectBuilder instanceof BuzzObjectBuilder) {
            $this->buzzObjectBuilder = new BuzzObjectBuilder();
        }
        return $this->buzzObjectBuilder;
    }

    /**
     * Set BuzzObjectBuilder
     * @param BuzzObjectBuilder $buzzObjectBuilder
     */
    public function setBuzzObjectBuilder(BuzzObjectBuilder $buzzObjectBuilder) {
        $this->buzzObjectBuilder = $buzzObjectBuilder;
    }

    /**
     * Get Latest shares from a given Share Id
     * 
     * @param type $recentShareId
     * @return array
     */
    public function getLatestBuzzShares($recentShareId = null) {
        $loggedInUserEmpNum = sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
        $latestShares = $this->getBuzzService()->getSharesUptoId($recentShareId);
        $postPhotosArray = array();
        foreach ($latestShares as $share) {
            $post = $share->getPostShared();
            $postPhotos = $this->getBuzzService()->getPostPhotos($post->getId());
            $postPhotosArray[$post->getId()] = $postPhotos;
        }
        return $this->getBuzzObjectBuilder()->getShareCollectionArray($latestShares, $postPhotosArray, $loggedInUserEmpNum);
    }

    /**
     * Get recent shares of Buzz
     * 
     * @param type $limit
     * @return array
     */
    public function getBuzzShares($limit = null) {
        $loggedInUserEmpNum = sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
        if (!$limit) {
            $limit = self::DEFAULT_SHARE_LIMIT;
        }
        $latestShares = $this->getBuzzService()->getShares($limit);
        $postPhotosArray = array();
        foreach ($latestShares as $share) {
            $post = $share->getPostShared();
            $postPhotos = $this->getBuzzService()->getPostPhotos($post->getId());
            $postPhotosArray[$post->getId()] = $postPhotos;
        }
        return $this->getBuzzObjectBuilder()->getShareCollectionArray($latestShares, $postPhotosArray, $loggedInUserEmpNum);
    }

    /**
     * Get more shares of Buzz
     * 
     * @param type $limit
     * @return array
     */
    public function getMoreBuzzShares($lastShareId, $limit) {
        $loggedInUserEmpNum = sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
        if (!$limit) {
            $limit = self::DEFAULT_SHARE_LIMIT;
        }
        $latestShares = $this->getBuzzService()->getMoreShares($limit, $lastShareId);
        $postPhotosArray = array();
        foreach ($latestShares as $share) {
            $post = $share->getPostShared();
            $postPhotos = $this->getBuzzService()->getPostPhotos($post->getId());
            $postPhotosArray[$post->getId()] = $postPhotos;
        }
        return $this->getBuzzObjectBuilder()->getShareCollectionArray($latestShares, $postPhotosArray, $loggedInUserEmpNum);
    }

    /**
     * 
     * @todo Break this down to several functions and test seperately
     * @param type $shareId
     * @return type
     */
    public function getShareAndPostDetailsByShareId($shareId) {
        $share = $this->getBuzzService()->getShareById($shareId);
        $post = $share->getPostShared();
        $postPhotos = $this->getBuzzService()->getPostPhotos($post->getId());

        return $this->getBuzzObjectBuilder()->getShareDetailsAsArray($share, $post, $postPhotos);
    }

    /**
     * Post content on news feed
     * @param type $loggedInEmployeeNumber
     * @param type $content
     * @param type $postedDateTime
     * @param type $extraPostOptions
     * @return type
     */
    public function postContentOnFeed($loggedInEmployeeNumber, $content, $postedDateTime, $extraPostOptions = null) {
        $post = $this->getBuzzObjectBuilder()->createPost($loggedInEmployeeNumber, $content, $postedDateTime);
        $share = $this->getBuzzObjectBuilder()->createShare($post, $postedDateTime);
        $share = $this->getBuzzService()->saveShare($share);
        if ($extraPostOptions) {
            $postId = $share->getPostId();
            $imagesArray = $this->getBuzzObjectBuilder()->extractImagesForPost($extraPostOptions, $postId);
            foreach ($imagesArray as $image) {
                $this->getBuzzService()->savePhoto($image);
            }
        }
        return $share->toArray();
    }

    /**
     * 
     * @param type $shareId
     * @param type $loggedInEmployeeNumber
     * @param type $commentText
     * @param type $postedDateTime
     * @return type
     */
    public function commentOnShare($shareId, $loggedInEmployeeNumber, $commentText, $postedDateTime) {
        $comment = $this->getBuzzObjectBuilder()->createCommentOnShare($shareId, $loggedInEmployeeNumber, $commentText, $postedDateTime);
        $result = $this->getBuzzService()->saveCommentShare($comment);
        return $result->toArray();
    }

    /**
     * Like on share
     * @param type $shareId
     * @param type $loggedInEmployeeNumber
     * @param type $postedDateTime
     * @return type
     */
    public function likeOnShare($shareId, $loggedInEmployeeNumber, $postedDateTime) {
        $likeOnShare = $this->getBuzzObjectBuilder()->createLikeOnShare($shareId, $loggedInEmployeeNumber, $postedDateTime);
        $dislikeOnShare = $this->getBuzzObjectBuilder()->createDislikeOnShare($shareId, $loggedInEmployeeNumber, $postedDateTime);

        $share = $this->getBuzzService()->getShareById($shareId);

        if ($share->isShareUnLike($loggedInEmployeeNumber)) {
            $this->getBuzzService()->deleteUnLikeForShare($dislikeOnShare);
        }

        if (!$share->isShareLike($loggedInEmployeeNumber)) {
            $this->getBuzzService()->saveLikeForShare($likeOnShare);
        }
        $shareSaved = $this->getBuzzService()->getShareById($shareId);
        return $shareSaved->toArray();
    }

    /**
     * Dislike on share
     * @param type $shareId
     * @param type $loggedInEmployeeNumber
     * @param type $postedDateTime
     * @return type
     */
    public function dislikeOnShare($shareId, $loggedInEmployeeNumber, $postedDateTime) {
        $likeOnShare = $this->getBuzzObjectBuilder()->createLikeOnShare($shareId, $loggedInEmployeeNumber, $postedDateTime);
        $dislikeOnShare = $this->getBuzzObjectBuilder()->createDislikeOnShare($shareId, $loggedInEmployeeNumber, $postedDateTime);

        $share = $this->getBuzzService()->getShareById($shareId);

        if ($share->isShareLike($loggedInEmployeeNumber)) {
            $this->getBuzzService()->deleteLikeForShare($likeOnShare);
        }

        if (!$share->isShareUnLike($loggedInEmployeeNumber)) {
            $this->getBuzzService()->saveUnLikeForShare($dislikeOnShare);
        }

        $shareSaved = $this->getBuzzService()->getShareById($shareId);
        return $shareSaved->toArray();
    }

    /**
     * Like on comment
     * @param type $commentId
     * @param type $loggedInEmployeeNumber
     * @param type $postedDateTime
     * @return type
     */
    public function likeOnComment($commentId, $loggedInEmployeeNumber, $postedDateTime) {
        $likeOnComment = $this->getBuzzObjectBuilder()->createLikeOnComment($commentId, $loggedInEmployeeNumber, $postedDateTime);
        $dislikeOnComment = $this->getBuzzObjectBuilder()->createDislikeOnComment($commentId, $loggedInEmployeeNumber, $postedDateTime);

        $comment = $this->getBuzzService()->getCommentById($commentId);

        if ($comment->isCommentUnLike($loggedInEmployeeNumber)) {
            $this->getBuzzService()->deleteUnLikeForComment($dislikeOnComment);
        }

        if (!$comment->isCommentLike($loggedInEmployeeNumber)) {
            $this->getBuzzService()->saveLikeForComment($likeOnComment);
        }

        $commentSaved = $this->getBuzzService()->getCommentById($commentId);
        return $commentSaved->toArray();
    }

    /**
     * Dislike on comment
     * @param type $commentId
     * @param type $loggedInEmployeeNumber
     * @param type $postedDateTime
     * @return type
     */
    public function dislikeOnComment($commentId, $loggedInEmployeeNumber, $postedDateTime) {
        $likeOnComment = $this->getBuzzObjectBuilder()->createLikeOnComment($commentId, $loggedInEmployeeNumber, $postedDateTime);
        $dislikeOnComment = $this->getBuzzObjectBuilder()->createDislikeOnComment($commentId, $loggedInEmployeeNumber, $postedDateTime);

        $comment = $this->getBuzzService()->getCommentById($commentId);

        if ($comment->isCommentLike($loggedInEmployeeNumber)) {
            $this->getBuzzService()->deleteLikeForComment($likeOnComment);
        }

        if (!$comment->isCommentUnLike($loggedInEmployeeNumber)) {
            $this->getBuzzService()->saveUnLikeForComment($dislikeOnComment);
        }

        $commentSaved = $this->getBuzzService()->getCommentById($commentId);

        return $commentSaved->toArray();
    }
    
    /**
     * get logged in employee object
     * @return Doctrine
     */
    
    public function getLoggedInEmployee(){
        $loggedInUserEmpNum =  sfContext::getInstance()->getUser()->getAttribute("auth.empNumber");
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        if($loggedInUserEmpNum != "" && !is_null($loggedInUserEmpNum)) {
            $loggedUser = $employeeService->getEmployee($loggedInUserEmpNum);
            $jobTitle = $loggedUser->getJobTitleName();
            $loggedUser->setCustom1($jobTitle);
            return $loggedUser->toArray();
        } else {
            $defaultAdmin  = array();
            $defaultAdmin['empNumber'] = "0";
            $defaultAdmin['firstName'] = "Admin";
            $defaultAdmin['lastName'] = "";
            $defaultAdmin['jobTitle'] = array(
                'jobTitleName' => 'Administrator'
            );
            $defaultAdmin['custom1'] = "Administrator";
            return $defaultAdmin;
        }

    }
    
    /**
     * Handles sharing post
     *
     * @param type $postId
     * @param type $loggedInEmployeeNumber
     * @param type $newText
     * @return type
     */
    public function sharePost($postId, $loggedInEmployeeNumber, $newText) {
        $response =array();
        $response["success"] = false;

        $shareDetails = $this->getBuzzService()->getSharePost($postId, $loggedInEmployeeNumber, $newText);
        $share = $this->getBuzzService()->saveShare($shareDetails);
        if($share instanceof Share) {
            $response["success"] = true;
        }
        $response["shareDetails"] = $share->toArray();
        return $response;
    }

    /**
     * Handles deleting share
     *
     * @param $shareId
     * @param $loggedInEmployeeNumber
     * @return array
     */
    public function deleteShare($shareId, $loggedInEmployeeNumber) {
        $isDeletedArray = array();
        $share = $this->getBuzzService()->getShareById($shareId);
        $isDeletedArray["success"] = false;
        if ($share instanceof Share && $share->getEmployeeNumber() == $loggedInEmployeeNumber) {
            $deleteShareResult = $this->getBuzzService()->deleteShare($shareId);
            if ($deleteShareResult == 1) {
                $isDeletedArray["success"] = true;
            }
        }

        return $isDeletedArray;
    }

    /**
     * Handles deleting comment
     *
     * @param $commentId
     * @param $loggedInEmployeeNumber
     * @return array
     */
    public function deleteCommentForShare($commentId, $loggedInEmployeeNumber) {
        $response = array();
        $response['success'] = false;
        $comment = $this->getBuzzService()->getCommentById($commentId);
        if($comment instanceof Comment && $comment->getEmployeeNumber() == $loggedInEmployeeNumber) {
            $deleteCommentResult = $this->getBuzzService()->deleteCommentForShare($comment);
            if ($deleteCommentResult == 1) {
                $response['success'] = true;
            }
        }
        return $response;
    }
    
    /**
     * Gets Employees Buzz Shares 
     * 
     * @param type $employeeNumber
     * @param type $loggedInEmpNumber
     * @return type
     */
    public function getBuzzForEmployee($employeeNumber, $loggedInEmpNumber) {    
        $employeeShares = $this->getBuzzService()->getSharesFromEmployeeNumber($employeeNumber);
        $postPhotosArray = array();
        foreach ($employeeShares as $share){
            $post = $share->getPostShared();
            $postPhotos = $this->getBuzzService()->getPostPhotos($post->getId());
            $postPhotosArray[$post->getId()] = $postPhotos;
        }
        return $this->getBuzzObjectBuilder()->getShareCollectionArray($employeeShares, $postPhotosArray, $loggedInEmpNumber);
    }

    /**
     * Gets the buzz image from the Id
     */
    public function getBuzzImage($imageId) {
        $request = sfContext::getInstance()->getRequest();
        $photo = $this->getBuzzService()->getPhoto($imageId);
        $response = sfContext::getInstance()->getResponse();
        $response = $this->getBuzzService()->getImageResponseWithCaching($photo,$request,$response);
        $response->send();
    }

    /**
     * Gets the image of employee from empNumber
     */
    public function getEmployeeImage($empNumber) {
        $request = sfContext::getInstance()->getRequest();
        $employeePicture = $this->getBuzzService()->getEmployeeService()->getEmployeePicture($empNumber);
        $response = sfContext::getInstance()->getResponse();
        $sfUser = sfContext::getInstance()->getUser();
        $response = $this->getBuzzService()->getEmployeeImageResponseWithCaching($employeePicture,$request,$response,$sfUser);
        $response->send();
    }
    

}