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
 * Description of BuzzObjectBuilderTest
 *
 * @author nirmal
 * @group buzz
 */
class BuzzObjectBuilderTest extends PHPUnit\Framework\TestCase {

    private $buzzObjectBuilder;

    /**
     * Set up method
     */
    protected function setUp(): void {
        $this->buzzObjectBuilder = new BuzzObjectBuilder();
    }

    /**
     * @covers BuzzObjectBuilder::getImageResizeUtility
     */
    public function testGetImageResizeUtility() {
        $imageResizeUtility = $this->buzzObjectBuilder->getImageResizeUtility();
        $this->assertTrue($imageResizeUtility instanceof ImageResizeUtility);
    }

    /**
     * @covers BuzzObjectBuilder::getBuzzConfigService
     */
    public function testGetBuzzConfigService() {
        $buzzConfigService = $this->buzzObjectBuilder->getBuzzConfigService();
        $this->assertTrue($buzzConfigService instanceof BuzzConfigService);
    }


    /**
     * @covers BuzzObjectBuilder::getShareCollectionArray
     */
    public function testGetShareCollectionArray() {
        $share = new Share();
        $share->setId(1);

        $post = new Post();
        $post->setId(2);

        $share->setPostShared($post);

        $shares = array(
            $share
        );

        $arrayOfShares = $this->buzzObjectBuilder->getShareCollectionArray($shares, array(1 => array(), 2 => array()), 1);
        $this->assertTrue(is_array($arrayOfShares));
        $this->assertEquals(1, count($arrayOfShares));
    }

    /**
     * @covers BuzzObjectBuilder::createShareDetailsArray
     */
    public function testCreateShareDetailsArray() {
        $share = new Share();
        $returnShareDetailsArray = $this->buzzObjectBuilder->createShareDetailsArray($share);
        $this->assertTrue(is_array($returnShareDetailsArray));
        $this->assertTrue(array_key_exists(BuzzObjectBuilder::KEY_SHARED_EMPLOYEE_DETAILS, $returnShareDetailsArray));
    }

    /**
     * @covers BuzzObjectBuilder::createPostDetailsArray
     */
    public function testCreatePostDetailsArray() {
        $post = new Post();
        $returnPostDetailsArray = $this->buzzObjectBuilder->createPostDetailsArray($post);
        $this->assertTrue(is_array($returnPostDetailsArray));
        $this->assertTrue(array_key_exists(BuzzObjectBuilder::KEY_POSTED_EMPLOYEE_DETAILS, $returnPostDetailsArray));
    }

    /**
     * @covers BuzzObjectBuilder::createPostPhotoDetailsArray
     */
    public function testCreatePostPhotoDetailsArray() {
        $postPhotos = array(
            new Photo()
        );

        $returnPostPhotoDetailsArray = $this->buzzObjectBuilder->createPostPhotoDetailsArray($postPhotos);
        $this->assertTrue(is_array($returnPostPhotoDetailsArray));
        $this->assertEquals(1, count($returnPostPhotoDetailsArray));
        $this->assertTrue(array_key_exists(BuzzObjectBuilder::KEY_POST_PHOTO_DETAILS, $returnPostPhotoDetailsArray[0]));
    }

    /**
     * @covers BuzzObjectBuilder::createPostPhotoDetailsArray
     */
    public function testCreatePostPhotoDetailsArrayWithWrongCollcetion() {
        $this->expectException('Exception');
        $postPhotos = array(
            new Employee()
        );

        $returnPostPhotoDetailsArray = $this->buzzObjectBuilder->createPostPhotoDetailsArray($postPhotos);
    }

    /**
     * @covers BuzzObjectBuilder::createCommentDetailsArray
     */
    public function testCreateCommentDetailsArray() {
        $comments = array(
            new Comment()
        );

        $returnCommentDetailsArray = $this->buzzObjectBuilder->createCommentDetailsArray($comments);
        $this->assertTrue(is_array($returnCommentDetailsArray));
        $this->assertEquals(1, count($returnCommentDetailsArray));
        $this->assertTrue(array_key_exists(BuzzObjectBuilder::KEY_COMMENT_DETAILS, $returnCommentDetailsArray[0]));
    }

    /**
     * @covers BuzzObjectBuilder::createCommentDetailsArray
     */
    public function testCreateCommentDetailsArrayWithWrongCollcetion() {
        $this->expectException('Exception');
        $comments = array(
            new Employee()
        );

        $returnCommentDetailsArray = $this->buzzObjectBuilder->createCommentDetailsArray($comments);
    }

    /**
     * @covers BuzzObjectBuilder::getShareDetailsAsArray
     */
    public function testGetShareDetailsAsArray() {
        $post = new Post();
        $share = new Share();
        $postPhotos = array(
            new Photo()
        );

        $returnShareDetailsArray = $this->buzzObjectBuilder->getShareDetailsAsArray($share, $post, $postPhotos);
        $this->assertTrue(array_key_exists(BuzzObjectBuilder::KEY_SHARE, $returnShareDetailsArray));
        $this->assertTrue(array_key_exists(BuzzObjectBuilder::KEY_LIKES_FOR_SHARE, $returnShareDetailsArray));
    }

    /**
     * @covers BuzzObjectBuilder::createPost
     */
    public function testCreatePost() {
        $loggedInEmployeeNumber = 1;
        $content = "test";
        $postedDateTime = date("Y-m-d H:i:s");

        $post = $this->buzzObjectBuilder->createPost($loggedInEmployeeNumber, $content, $postedDateTime);
        $this->assertTrue($post instanceof Post);
        $this->assertEquals($content, $post->getText());
    }

    /**
     * @covers BuzzObjectBuilder::createShare
     */
    public function testCreateShare() {
        $loggedInEmployeeNumber = 1;
        $content = "test";
        $postedDateTime = date("Y-m-d H:i:s");

        $post = new Post();
        $post->setEmployeeNumber($loggedInEmployeeNumber);
        $post->setText($content);
        $post->setPostTime($postedDateTime);

        $share = $this->buzzObjectBuilder->createShare($post, $postedDateTime);
        $this->assertTrue($share instanceof Share);
        $this->assertEquals($loggedInEmployeeNumber, $share->getEmployeeNumber());
    }

    /**
     * @covers BuzzObjectBuilder::createShare
     */
    public function testCreateShareWithWrongParametersForPost() {
        $this->expectException('Exception');
        $postedDateTime = date("Y-m-d H:i:s");
        $post = new Employee();

        $share = $this->buzzObjectBuilder->createShare($post, $postedDateTime);
    }

    /**
     * @covers BuzzObjectBuilder::createCommentOnShare
     */
    public function testCreateCommentOnShare() {
        $shareId = 1;
        $loggedInEmployeeNumber = 1;
        $commentText = "test";
        $postedDateTime = date("Y-m-d H:i:s");

        $comment = $this->buzzObjectBuilder->createCommentOnShare($shareId, $loggedInEmployeeNumber, $commentText, $postedDateTime);
        $this->assertTrue($comment instanceof Comment);
    }

    /**
     * @covers BuzzObjectBuilder::createCommentOnShare
     */
    public function testCreateCommentOnShareWithoutShareId() {
        $this->expectException('Exception');
        $shareId = null;
        $loggedInEmployeeNumber = 1;
        $commentText = "test";
        $postedDateTime = date("Y-m-d H:i:s");

        $comment = $this->buzzObjectBuilder->createCommentOnShare($shareId, $loggedInEmployeeNumber, $commentText, $postedDateTime);
    }

    /**
     * @covers BuzzObjectBuilder::createLikeOnShare
     */
    public function testCreateLikeOnShare() {
        $shareId = 1;
        $loggedInEmployeeNumber = 1;
        $testDateTime = date("Y-m-d H:i:s");

        $likeOnShare = $this->buzzObjectBuilder->createLikeOnShare($shareId, $loggedInEmployeeNumber, $testDateTime);
        $this->assertTrue($likeOnShare instanceof LikeOnShare);
    }

    /**
     * @covers BuzzObjectBuilder::CreateLikeOnShare
     */
    public function testCreateLikeOnShareWithoutShareId() {
        $this->expectException('Exception');
        $shareId = null;
        $loggedInEmployeeNumber = 1;
        $testDateTime = date("Y-m-d H:i:s");

        $likeOnShare = $this->buzzObjectBuilder->createLikeOnShare($shareId, $loggedInEmployeeNumber, $testDateTime);
    }

    /**
     * @covers BuzzObjectBuilder::createDislikeOnShare
     */
    public function testCreateDislikeOnShare() {
        $shareId = 1;
        $loggedInEmployeeNumber = 1;
        $testDateTime = date("Y-m-d H:i:s");

        $dislikeOnShare = $this->buzzObjectBuilder->createDislikeOnShare($shareId, $loggedInEmployeeNumber, $testDateTime);
        $this->assertTrue($dislikeOnShare instanceof UnLikeOnShare);
    }

    /**
     * @covers BuzzObjectBuilder::createDislikeOnShare
     */
    public function testCreateDislikeOnShareWithoutShareId() {
        $this->expectException('Exception');
        $shareId = null;
        $loggedInEmployeeNumber = 1;
        $testDateTime = date("Y-m-d H:i:s");

        $dislikeOnShare = $this->buzzObjectBuilder->createDislikeOnShare($shareId, $loggedInEmployeeNumber, $testDateTime);
    }

    /**
     * @covers BuzzObjectBuilder::createLikeOnComment
     */
    public function testCreateLikeOnComment() {
        $commentId = 1;
        $loggedInEmployeeNumber = 1;
        $testDateTime = date("Y-m-d H:i:s");

        $likeOnComment = $this->buzzObjectBuilder->createLikeOnComment($commentId, $loggedInEmployeeNumber, $testDateTime);
        $this->assertTrue($likeOnComment instanceof LikeOnComment);
    }

    /**
     * @covers BuzzObjectBuilder::createLikeOnComment
     */
    public function testCreateLikeOnCommentWithoutCommentId() {
        $this->expectException('Exception');
        $commentId = null;
        $loggedInEmployeeNumber = 1;
        $testDateTime = date("Y-m-d H:i:s");

        $likeOnComment = $this->buzzObjectBuilder->createLikeOnComment($commentId, $loggedInEmployeeNumber, $testDateTime);
    }

    /**
     * @covers BuzzObjectBuilder::createDislikeOnComment
     */
    public function testCreateDislikeOnComment() {
        $commentId = 1;
        $loggedInEmployeeNumber = 1;
        $testDateTime = date("Y-m-d H:i:s");

        $dislikeOnComment = $this->buzzObjectBuilder->createDislikeOnComment($commentId, $loggedInEmployeeNumber, $testDateTime);
        $this->assertTrue($dislikeOnComment instanceof UnLikeOnComment);
    }

    /**
     * @covers BuzzObjectBuilder::createDislikeOnComment
     */
    public function testCreateDislikeOnCommentWithoutCommentId() {
        $this->expectException('Exception');
        $commentId = null;
        $loggedInEmployeeNumber = 1;
        $testDateTime = date("Y-m-d H:i:s");

        $dislikeOnComment = $this->buzzObjectBuilder->createDislikeOnComment($commentId, $loggedInEmployeeNumber, $testDateTime);
    }

    /**
     * @covers BuzzObjectBuilder::extractImagesForPost
     */
    public function testExtractImagesForPostWithCorrectData() {
        $postId = 1;

        $imageDataArray = array(
            array(
                BuzzObjectBuilder::KEY_IMAGE_STRING_ENCODED => $this->getBase64EncodedImageString(),
                BuzzObjectBuilder::KEY_IMAGE_NAME => 'test_image',
                BuzzObjectBuilder::KEY_IMAGE_TYPE => 'jpg'
            ),
            array(
                BuzzObjectBuilder::KEY_IMAGE_STRING_ENCODED => $this->getBase64EncodedImageString(),
                BuzzObjectBuilder::KEY_IMAGE_NAME => 'test_image',
                BuzzObjectBuilder::KEY_IMAGE_TYPE => 'jpg'
            ),
        );

        $extraPostOptions = json_encode($imageDataArray);

        $photoArray = $this->buzzObjectBuilder->extractImagesForPost($extraPostOptions, $postId);
        $this->assertTrue(is_array($photoArray));
        $this->assertEquals(2, count($photoArray));
        $this->assertTrue($photoArray[0] instanceof Photo);
    }

    /**
     * @covers BuzzObjectBuilder::extractImagesForPost
     */
    public function testExtractImagesForPostWithIncorrectData() {
        $this->expectException('Exception');
        $postId = 1;

        $imageDataArray = array(
            array(
                BuzzObjectBuilder::KEY_IMAGE_STRING_ENCODED => $this->getBase64EncodedImageString(),
                BuzzObjectBuilder::KEY_IMAGE_NAME => 'test_image',
                BuzzObjectBuilder::KEY_IMAGE_TYPE => 'jpg'
            ),
            array(
                BuzzObjectBuilder::KEY_IMAGE_STRING_ENCODED => $this->getBase64EncodedImageString(),
                BuzzObjectBuilder::KEY_IMAGE_NAME => 'test_image',
                BuzzObjectBuilder::KEY_IMAGE_TYPE => 'jpg'
            ),
        );

        $extraPostOptions = array(
            BuzzObjectBuilder::KEY_IMAGE_DATA => $imageDataArray
        );

        $photoArray = $this->buzzObjectBuilder->extractImagesForPost($extraPostOptions, $postId);
    }

    /**
     * @covers BuzzObjectBuilder::createPhoto
     */
    public function testCreatePhoto() {
        $postId = 1;

        $imageDetailsArray = array(
            BuzzObjectBuilder::KEY_IMAGE_STRING_ENCODED => $this->getBase64EncodedImageString(),
            BuzzObjectBuilder::KEY_IMAGE_NAME => 'test_image',
            BuzzObjectBuilder::KEY_IMAGE_TYPE => 'jpg'
        );

        $returnPhoto = $this->buzzObjectBuilder->createPhoto($imageDetailsArray, $postId);
        $this->assertTrue($returnPhoto instanceof Photo);
        $this->assertEquals(test_image, $returnPhoto->getFilename());
        $this->assertEquals(265, $returnPhoto->getHeight());
    }

    /**
     * @covers BuzzObjectBuilder::createPhoto
     */
    public function testCreatePhotoWithWrongParameters() {

        $imageDetailsArray = array(
            BuzzObjectBuilder::KEY_IMAGE_STRING_ENCODED => null,
            BuzzObjectBuilder::KEY_IMAGE_NAME => 'test_image',
            BuzzObjectBuilder::KEY_IMAGE_TYPE => 'jpg'
        );

        $returnPhoto = $this->buzzObjectBuilder->createPhoto($imageDetailsArray, null);
        $this->assertEquals(0, $returnPhoto);
    }

    private function getBase64EncodedImageString() {
        return '/9j/4AAQSkZJRgABAQAAAQABAAD/4QBoRXhpZgAASUkqAAgAAAADABIBAwABAAAAAQAAADEBAgAQ AAAAMgAAAGmHBAABAAAAQgAAAAAAAABTaG90d2VsbCAwLjE4LjAAAgACoAkAAQAAAAkBAAADoAkA AQAAAAkBAAAAAAAA/+EJ9Gh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJl Z2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxu czp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNC40LjAtRXhpdjIiPiA8cmRm OlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1u cyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczpleGlmPSJodHRwOi8vbnMu YWRvYmUuY29tL2V4aWYvMS4wLyIgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZm LzEuMC8iIGV4aWY6UGl4ZWxYRGltZW5zaW9uPSIyNjUiIGV4aWY6UGl4ZWxZRGltZW5zaW9uPSIy NjUiIHRpZmY6SW1hZ2VXaWR0aD0iMjY1IiB0aWZmOkltYWdlSGVpZ2h0PSIyNjUiIHRpZmY6T3Jp ZW50YXRpb249IjEiLz4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3hwYWNrZXQgZW5kPSJ3Ij8+/9sA QwADAgIDAgIDAwMDBAMDBAUIBQUEBAUKBwcGCAwKDAwLCgsLDQ4SEA0OEQ4LCxAWEBETFBUVFQwP FxgWFBgSFBUU/9sAQwEDBAQFBAUJBQUJFA0LDRQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQU FBQUFBQUFBQUFBQUFBQUFBQUFBQU/8AAEQgBCQEJAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAA AAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQy gZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVm Z2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS 09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYH CAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1Lw FWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5 eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj 5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A+t/+GqrL/oXrj/wJX/4mj/hqqy/6F64/8CV/ +Jr51or+Z/8AXzOv5o/+Ao/fv9Tsq/ll/wCBH0V/w1VZf9C9cf8AgSv/AMTR/wANVWX/AEL1x/4E r/8AE1860Uf6+Z1/NH/wFB/qdlX8sv8AwI+iv+GqrL/oXrj/AMCV/wDiaP8Ahqqy/wCheuP/AAJX /wCJr51oo/18zr+aP/gKD/U7Kv5Zf+BH0V/w1VZf9C9cf+BK/wDxNH/DVVl/0L1x/wCBK/8AxNfO tFH+vmdfzR/8BQf6nZV/LL/wI+iv+GqrL/oXrj/wJX/4mj/hqqy/6F64/wDAlf8A4mvnWij/AF8z r+aP/gKD/U7Kv5Zf+BH0V/w1VZf9C9cf+BK//E0f8NVWX/QvXH/gSv8A8TXzrRR/r5nX80f/AAFB /qdlX8sv/Aj6K/4aqsv+heuP/Alf/iaP+GqrL/oXrj/wJX/4mvnWij/XzOv5o/8AgKD/AFOyr+WX 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';
    }

}
