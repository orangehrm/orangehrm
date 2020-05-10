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
 * Description of uploadImage
 *
 * @author aruna
 */
class uploadImageAction extends BaseBuzzAction {

    protected $photos = array();

    /**
     * @param sfForm $form
     * @return
     */
    protected function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {
        try {

            $token = $request->getParameter('csrfToken');
            $this->setForm(new ImageUploadForm());
            if ($this->form->getCSRFToken() == $token) {
                $this->loggedInUser = $this->getLogedInEmployeeNumber();
                if ($this->loggedInUser) {
                    $loggedInEmployee = $this->getEmployeeService()->getEmployee($this->loggedInUser);
                }

                $this->files = $request->getFiles();
                $postContent = $request->getParameter('postContent');
                $this->savePost($postContent, $loggedInEmployee);
                foreach ($this->files as $file) {
                    $photo = $this->getPhoto($file);
                    $this->savePhoto($photo);
                }
                $this->saveShare();
            }
        } catch (Exception $ex) {
            $logger = Logger::getLogger('buzz');
            $logger->error('Exception when uploading image: ' . $ex);

            $response = $this->getResponse();
            $response->setStatusCode(500, __('Error uploading image'));
            return sfView::NONE;
        }
    }

    /**
     * saving photo to the database
     * @param type $photo
     */
    private function savePhoto($photo) {
        $service = $this->getBuzzService();
        $service->savePhoto($photo);
    }

    /**
     * get photo from the request content
     * @param type $file
     * @return \Photo
     */
    private function getPhoto($file) {
        $photo = new Photo();

        $buzzConfigService = $this->getBuzzConfigService();
        $maxDimension = $buzzConfigService->getMaxImageDimension();

        $imageUtility = new ImageResizeUtility();
        $imageData = $imageUtility->convertUploadedImage($file['tmp_name'], $maxDimension, $maxDimension);

        $photo->photo = $imageData['image'];
        $photo->filename = $file['name'];
        $photo->file_type = $file['type'];
        $photo->setHeight($imageData['height']);
        $photo->setWidth($imageData['width']);
        $photo->size = strlen($imageData['image']);
        $photo->post_id = $this->post->getId();
        return $photo;
    }

    /**
     * save post to datebase
     * @param type $postContent
     */
    private function savePost($postContent, $employee) {
        $post = new Post();
        $post->setEmployeeNumber($this->getLogedInEmployeeNumber());
        $post->setText($postContent);
        $post->setPostTime(date("Y-m-d H:i:s"));
        $post->setUpdatedAt(date("Y-m-d H:i:s"));
        $service = $this->getBuzzService();
        $this->post = $service->savePost($post);
    }

    /**
     * 
     * @param Doctrine_Collection $photos
     */
    private function saveShare() {
        $share = new Share();
        $share->setEmployeeNumber($this->getLogedInEmployeeNumber());
        $share->setShareTime(date("Y-m-d H:i:s"));
        $share->setUpdatedAt(date("Y-m-d H:i:s"));
        $share->setType(0);
        $share->setNumberOfComments(0);
        $share->setNumberOfLikes(0);
        $share->setNumberOfUnlikes(0);
        $share->setPostId($this->post->getId());
        $service = $this->getBuzzService();
        $service->saveShare($share);
        $this->share = $share;
    }

}
