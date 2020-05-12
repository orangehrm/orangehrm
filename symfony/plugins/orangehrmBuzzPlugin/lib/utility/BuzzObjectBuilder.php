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
 * Description of BuzzObjectBuilder
 *
 * @author nirmal
 */
class BuzzObjectBuilder {

    const KEY_SHARE = 'share';
    const KEY_POST = 'post';
    const KEY_COMMENTS = 'comments';
    const KEY_PHOTOS = 'photos';
    const KEY_LIKES_FOR_SHARE = 'likes_for_share';
    const KEY_DISLIKES_FOR_SHARE = 'dislikes_for_share';
    const KEY_SHARE_POST_FOR_SHARE = 'share_post_for_share';
    const KEY_SHARES_FOR_SHARE = 'number_of_shares';
    const KEY_SHARE_DETAILS = 'share_details';
    const KEY_SHARED_EMPLOYEE_DETAILS = 'shared_employee_details';
    const KEY_EMPLOYEE_NAME = 'employee_name';
    const KEY_EMPLOYEE_NUMBER = 'employee_number';
    const KEY_POST_DETAILS = 'post_details';
    const KEY_POSTED_EMPLOYEE_DETAILS = 'posted_employee_details';
    const KEY_POST_PHOTO_DETAILS = 'posted_photo_details';
    const KEY_POST_PHOTO_URL = 'posted_photo_url';
    const KEY_COMMENT_DETAILS = 'comment_details';
    const KEY_COMMENTED_EMPLOYEE_DETAILS = 'commented_employee_details';
    const KEY_LIKES_FOR_COMMENT = 'likes_for_comment';
    const KEY_IMAGE_DATA = 'image_data';
    const KEY_IMAGE_NAME = 'image_name';
    const KEY_IMAGE_TYPE = 'image_type';
    const KEY_IMAGE_STRING_ENCODED = 'image_string_encoded';
    const KEY_DISLIKES_FOR_COMMENT = 'dislikes_for_comment';
    const KEY_IS_LIKE = "is_like";
    const KEY_IS_DISLIKE = "is_dislike";
    const BUZZ_VIEW_EMPLOYEE_PHOTO_BASE_URL = 'buzz/viewPhoto?empNumber=';
    const BUZZ_VIEW_PHOTO_BASE_URL = 'buzz/viewBuzzImage?imageId=';
    const DEFAULT_START_NUMBER = 0;
    const IMAGE_WIDTH_INDEX = 0;
    const IMAGE_HEIGHT_INDEX = 1;
    const KEY_LINK = "link";

    protected $imageResizeUtility;
    protected $buzzConfigService;

    /**
     * Set image resize utility
     * @param ImageResizeUtility $imageResizeUtility
     */
    public function setImageResizeUtility(ImageResizeUtility $imageResizeUtility) {
        $this->imageResizeUtility = $imageResizeUtility;
    }

    /**
     * Get Image resize utility
     * @return ImageResizeUtility
     */
    public function getImageResizeUtility() {
        if (!$this->imageResizeUtility instanceof ImageResizeUtility) {
            $this->imageResizeUtility = new ImageResizeUtility();
        }
        return $this->imageResizeUtility;
    }

    /**
     * Set Buzz config service
     * @param BuzzConfigService $buzzConfigService
     */
    public function setBuzzConfigService(BuzzConfigService $buzzConfigService) {
        $this->buzzConfigService = $buzzConfigService;
    }

    /**
     * Get Buzz Configuration Service
     * @return BuzzConfigService
     */
    public function getBuzzConfigService() {
        if (!$this->buzzConfigService instanceof BuzzConfigService) {
            $this->buzzConfigService = new BuzzConfigService();
        }
        return $this->buzzConfigService;
    }
    
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
     * Get shares array with post details once passed a share collection 
     * @param Doctrine_Collection $shares
     * @return array
     */
    public function getShareCollectionArray($shares, $postPhotosArray, $loggedEmployeeNumber) {

        $returnShareArray = array();
        foreach ($shares as $share) {
            try {
                if (!($share instanceof Share)) {
                    throw new Exception("Invalid Type");
                }
                $singleShareAndPostDetailsArray[self::KEY_SHARE] = $this->createShareDetailsArray($share);
                $post = $share->getPostShared();
                $singleShareAndPostDetailsArray[self::KEY_POST] = $this->createPostDetailsArray($post);
                $singleShareAndPostDetailsArray[self::KEY_IS_LIKE] = $share->isShareLike($loggedEmployeeNumber);
                $singleShareAndPostDetailsArray[self::KEY_IS_DISLIKE] = $share->isShareUnlike($loggedEmployeeNumber);
                $singleShareAndPostDetailsArray[self::KEY_POST][self::KEY_PHOTOS] = $this->createPostPhotoDetailsArray($postPhotosArray[$post->getId()]);
                $returnShareArray[] = $singleShareAndPostDetailsArray;
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        return $returnShareArray;
    }

    /**
     * Create share details array
     * @param Share $share
     * @return array
     */
    public function createShareDetailsArray(Share $share) {
        $shareDetailsArray = array();
        $shareDetailsArray[self::KEY_SHARE_DETAILS] = $share->toArray();
        $shareDetailsArray[self::KEY_SHARE_DETAILS][self::KEY_SHARES_FOR_SHARE] = "" . $share->calShareCount();
        $shareDetailsArray[self::KEY_SHARED_EMPLOYEE_DETAILS][self::KEY_EMPLOYEE_NAME] = $share->getEmployeeFirstLastName();
        $shareDetailsArray[self::KEY_SHARED_EMPLOYEE_DETAILS][self::KEY_EMPLOYEE_NUMBER] = $share->getEmployeeNumber();

        return $shareDetailsArray;
    }

    /**
     * Create post details array
     * @param Post $post
     * @return array
     */
    public function createPostDetailsArray(Post $post) {
        $postDetailsArray = array();
        $postDetailsArray[self::KEY_POST_DETAILS] = $post->toArray();
        $postDetailsArray[self::KEY_POSTED_EMPLOYEE_DETAILS][self::KEY_EMPLOYEE_NAME] = $post->getEmployeeFirstLastName();
        $postDetailsArray[self::KEY_POSTED_EMPLOYEE_DETAILS][self::KEY_EMPLOYEE_NUMBER] = $post->getEmployeeNumber();
        $postDetailsArray[self::KEY_LINK] = $post->getLinks()->toArray();

        return $postDetailsArray;
    }

    /**
     * Create post photo details array
     * @param Doctrine_Collection $postPhotos
     * @return array
     */
    public function createPostPhotoDetailsArray($postPhotos) {
        try {
            $postPhotosArray = array();
            foreach ($postPhotos as $postPhoto) {
                $returnPostPhoto = array();
                $returnPostPhoto[self::KEY_POST_PHOTO_URL] = url_for(self::BUZZ_VIEW_PHOTO_BASE_URL . $postPhoto->getId());
                $returnPostPhoto[self::KEY_POST_PHOTO_DETAILS] = $postPhoto->toArray();
                $postPhotosArray[] = $returnPostPhoto;
            }

            return $postPhotosArray;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Create comment details array
     * @param Doctrine_Collection $comments
     * @return array
     */
    public function createCommentDetailsArray($comments) {
        try {
            $commentsArray = array();
            foreach ($comments as $comment) {
                $commentDetailsArray = array();
                $commentDetailsArray[self::KEY_COMMENT_DETAILS] = $comment->toArray();
                $commentDetailsArray[self::KEY_COMMENTED_EMPLOYEE_DETAILS][self::KEY_EMPLOYEE_NAME] = $comment->getEmployeeFirstLastName();
                $commentDetailsArray[self::KEY_COMMENTED_EMPLOYEE_DETAILS][self::KEY_EMPLOYEE_NUMBER] = $comment->getEmployeeNumber();

                $likesForComment = $comment->getCommentLikedEmployeeList();
                $commentDetailsArray[self::KEY_LIKES_FOR_COMMENT] = $likesForComment;

                $dislikesForComment = $comment->getCommentDislikedEmployeeList();
                $commentDetailsArray[self::KEY_DISLIKES_FOR_COMMENT] = $dislikesForComment;
                $commentsArray[] = $commentDetailsArray;
            }

            return $commentsArray;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Build and return the share details array
     * @param Share $share
     * @param Post $post
     * @param Doctrine_Collection $postPhotos
     * @return array
     * @throws Exception
     */
    public function getShareDetailsAsArray(Share $share, Post $post, $postPhotos) {
        try {
            $returnShareAndPostDetails = array();

            $returnShareAndPostDetails[self::KEY_SHARE] = $this->createShareDetailsArray($share);
            $returnShareAndPostDetails[self::KEY_POST] = $this->createPostDetailsArray($post);
            $returnShareAndPostDetails[self::KEY_POST][self::KEY_PHOTOS] = $this->createPostPhotoDetailsArray($postPhotos);
            $returnShareAndPostDetails[self::KEY_LIKES_FOR_SHARE] = $share->getShareLikedEmployeeList();
            $returnShareAndPostDetails[self::KEY_DISLIKES_FOR_SHARE] = $share->getShareDislikedEmployeeList();
            $returnShareAndPostDetails[self::KEY_SHARE_POST_FOR_SHARE] = $this->getBuzzService()->getSharedEmployeeNames($share);
            $returnShareAndPostDetails[self::KEY_COMMENTS] = $this->createCommentDetailsArray($share->getComment());

            return $returnShareAndPostDetails;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Create post 
     * @param type $loggedInEmployeeNumber
     * @param type $content
     * @param type $postedDateTime
     * @return Post
     */
    public function createPost($loggedInEmployeeNumber, $content, $postedDateTime) {
        $post = new Post();
        $post->setEmployeeNumber($loggedInEmployeeNumber);
        $post->setText($content);
        $post->setPostTime($postedDateTime);
        $post->setUpdatedAt($postedDateTime);

        return $post;
    }

    /**
     * Create share
     * @param Post $post
     * @param type $postedDateTime
     * @return \Share
     */
    public function createShare($post, $postedDateTime) {
        try {
            $share = new Share();
            $share->setPostShared($post);
            $share->setPostId($post->getId());
            $share->setEmployeeNumber($post->getEmployeeNumber());
            $share->setNumberOfComments(self::DEFAULT_START_NUMBER);
            $share->setNumberOfLikes(self::DEFAULT_START_NUMBER);
            $share->setNumberOfUnlikes(self::DEFAULT_START_NUMBER);
            $share->setShareTime($postedDateTime);
            $share->setUpdatedAt($postedDateTime);
            $share->setType(self::DEFAULT_START_NUMBER);
            return $share;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Create Comment on share
     * @param type $shareId
     * @param type $loggedInEmployeeNumber
     * @param type $commentText
     * @param type $postedDateTime
     * @return \Comment
     */
    public function createCommentOnShare($shareId, $loggedInEmployeeNumber, $commentText, $postedDateTime) {
        if (!$shareId || !$postedDateTime) {
            throw new Exception();
        } else {
            $comment = new Comment();
            $comment->setShareId($shareId);
            $comment->setEmployeeNumber($loggedInEmployeeNumber);
            $comment->setCommentText($commentText);
            $comment->setCommentTime($postedDateTime);
            $comment->setUpdatedAt($postedDateTime);
            $comment->setNumberOfLikes(self::DEFAULT_START_NUMBER);
            $comment->setNumberOfUnlikes(self::DEFAULT_START_NUMBER);

            return $comment;
        }
    }

    /**
     * Create like object on share
     * @param type $shareId
     * @param type $loggedInEmployeeNumber
     * @param type $postedDateTime
     * @return \LikeOnShare
     */
    public function createLikeOnShare($shareId, $loggedInEmployeeNumber, $postedDateTime) {
        if (!$shareId || !$postedDateTime) {
            throw new Exception();
        } else {
            $likeOnShare = New LikeOnShare();
            $likeOnShare->setLikeTime($postedDateTime);
            $likeOnShare->setEmployeeNumber($loggedInEmployeeNumber);
            $likeOnShare->setShareId($shareId);
            return $likeOnShare;
        }
    }

    /**
     * Create Dislike Object on share
     * @param type $shareId
     * @param type $loggedInEmployeeNumber
     * @param type $postedDateTime
     * @return \UnLikeOnShare
     */
    public function createDislikeOnShare($shareId, $loggedInEmployeeNumber, $postedDateTime) {
        if (!$shareId || !$postedDateTime) {
            throw new Exception();
        } else {
            $dislikeOnShare = New UnLikeOnShare();
            $dislikeOnShare->setLikeTime($postedDateTime);
            $dislikeOnShare->setEmployeeNumber($loggedInEmployeeNumber);
            $dislikeOnShare->setShareId($shareId);
            return $dislikeOnShare;
        }
    }

    /**
     * Create like Object on comment
     * @param type $commentId
     * @param type $loggedInEmployeeNumber
     * @param type $postedDateTime
     * @return \LikeOnComment
     */
    public function createLikeOnComment($commentId, $loggedInEmployeeNumber, $postedDateTime) {
        if (!$commentId || !$postedDateTime) {
            throw new Exception();
        } else {
            $likeOnComment = New LikeOnComment();
            $likeOnComment->setLikeTime($postedDateTime);
            $likeOnComment->setEmployeeNumber($loggedInEmployeeNumber);
            $likeOnComment->setCommentId($commentId);
            return $likeOnComment;
        }
    }

    /**
     * Create Dislike Object on comment
     * @param type $commentId
     * @param type $loggedInEmployeeNumber
     * @param type $postedDateTime
     * @return \UnLikeOnComment
     */
    public function createDislikeOnComment($commentId, $loggedInEmployeeNumber, $postedDateTime) {
        if (!$commentId || !$postedDateTime) {
            throw new Exception();
        } else {
            $dislikeOnComment = New UnLikeOnComment();
            $dislikeOnComment->setLikeTime($postedDateTime);
            $dislikeOnComment->setEmployeeNumber($loggedInEmployeeNumber);
            $dislikeOnComment->setCommentId($commentId);
            return $dislikeOnComment;
        }
    }

    /**
     * 
     * @param array $extraPostOptions
     * @param type $postId
     * @return \Photo
     * @throws Exception
     */
    public function extractImagesForPost($extraPostOptions, $postId) {
        try {
            $imagesArray = array();

            if ($extraPostOptions[self::KEY_IMAGE_DATA]) {
                $allImages = json_decode($extraPostOptions, true);

                if (is_array($allImages)) {
                    foreach ($allImages as $image) {
                        $photo = $this->createPhoto($image, $postId);
                        $imagesArray[] = $photo;
                    }
                } else {
                    throw new Exception("invalid json");
                }
            }
            return $imagesArray;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Create Image by image details array
     * @param array $imageDetailsArray
     * @param type $postId
     * @return \Photo
     */
    public function createPhoto($imageDetailsArray, $postId) {
        try {
            $imageDataEncoded = $imageDetailsArray[self::KEY_IMAGE_STRING_ENCODED];
            $imageDataDecoded = base64_decode($imageDataEncoded);
            $imageName = $imageDetailsArray[self::KEY_IMAGE_NAME];
            $imageType = $imageDetailsArray[self::KEY_IMAGE_TYPE];
            $imageWithAndHeight = getimagesizefromstring($imageDataDecoded);
            $imageWidth = $imageWithAndHeight[self::IMAGE_WIDTH_INDEX];
            $imageHeight = $imageWithAndHeight[self::IMAGE_HEIGHT_INDEX];

            if ($imageDataDecoded) {
                $photo = new Photo();
                $photo->setPhoto($imageDataDecoded);
                $photo->setFilename($imageName);
                $photo->setFileType($imageType);
                $photo->setHeight($imageHeight);
                $photo->setWidth($imageWidth);
                $photo->setSize(strlen($imageDataDecoded));
                $photo->setPostId($postId);
                return $photo;
            } else {
                return 0;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

}
