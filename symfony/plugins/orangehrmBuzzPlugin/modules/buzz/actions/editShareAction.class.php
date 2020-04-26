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
 * Description of likeOnShareAction
 *
 * @author aruna
 */
class editShareAction extends BaseBuzzAction {

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
            $this->setForm(new DeleteOrEditShareForm());

            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $formValues = $this->form->getValues();
                    $this->shareId = $formValues['shareId'];
                    $this->editedContent = $formValues['textShare'];

                    $this->loggedInUser = $this->getLogedInEmployeeNumber();
                    $this->type = 'post';
                    $this->error = 'no';

                    $share = $this->getBuzzService()->getShareById($this->shareId);
                    if ($share != null) {
                        $this->post = $this->saveEditedContent($share);
                    } else {
                        $this->error = 'yes';
                        $this->getUser()->setFlash('error', __("This share has been deleted or you do not have permission to perform this action"));
                    }
                }
            }
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }
    }

    /**
     * save edited content of post and share
     * @return Post
     */
    public function saveEditedContent($share) {

        if ($share->getEmployeeNumber() == $this->getLogedInEmployeeNumber()) {
            if ($share->getType() == 1) {
                $this->type = 'share';
                $share = $this->saveShare($share);
            } else {

                $share = $this->savePost($share->getPostShared());
            }
        }
        return $share;
    }

    /**
     * save post to the database
     * @param Post $post
     * @return Post
     */
    public function savePost($post) {

        $post->setText($this->editedContent);
        $links = $post->getLinks();
        if($links->count() > 0){
            $url =  $this->getBuzzService()->updateLinks($this->editedContent);
            $urls = $this->getBuzzService()->getUrlsArray($url);
            if($links->count() <= count($urls)){
                $i = 0;
                foreach($links as $link){
                    $link->setLink($urls[$i]);
                    $link->save();
                    $i++;
                }
            }
        }
        return $this->getBuzzService()->savePost($post);
    }

    /**
     * savbe share to the database
     * @param Share $share
     * @return Share
     */
    public function saveShare($share) {
        $share->setText($this->editedContent);
        return $this->getBuzzService()->saveShare($share);
    }

}
