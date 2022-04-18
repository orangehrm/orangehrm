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
class CreateVideoForm extends sfForm {

    private $widgets;

    /**
     * Defining widgets and thier default values
     */
    public function configure() {
        $textArea = new sfWidgetFormTextarea();
        $placeholder = __("Paste Video Url");
        $textArea->setAttribute('placeholder', $placeholder);
        $textArea->setAttribute('rows', '1');

        $linkAddress = new sfWidgetFormTextarea();
        $linkAddress->setAttribute('hidden', 'hidden');
        $linkAddress->setAttribute('class', 'linkAddress');

        $this->widgets = array(
            'content' => $textArea,
            'linkAddress' => $linkAddress
        );
        $this->setWidgets($this->widgets);
        $this->widgetSchema->setNameFormat('createVideo[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
        $this->assignValidators();
    }

    /**
     * Defining the validators for the widgets
     */
    public function assignValidators() {
        $this->setValidators(array(
            'content' => new sfValidatorString(array('required' => true)),
            'linkAddress' => new sfValidatorString(array('required' => true))
        ));
    }

    /**
     * this is function to get buzzService
     * @return BuzzService 
     */
    public function getBuzzService() {
        if (!$this->buzzService) {
            $this->buzzService = new BuzzService();
        }
        return $this->buzzService;
    }

    /**
     * Get the label texts for the form widgets
     * @return array Label Texts
     */
    protected function getFormLabels() {

        $labels = array(
            'content' => ' ',
            'linkAddress' => ' '
        );
        return $labels;
    }

    /**
     * save share and post to database
     * @param int $logeInUserId
     * @return Share
     */
    public function save($logeInUserId) {
        $post = $this->savePost($logeInUserId);
        $share = $this->saveShare($post);
        if (strlen($this->getValue('linkAddress')) > 0) {
            $link = $this->setLink($share);
            $this->saveLink($link);
        }
        return $share;
    }

    /**
     * save post to the database
     * @return Post
     */
    public function savePost($userId) {
        $post = new Post();
        $post->setEmployeeNumber($userId);
        $post->setText($this->getValue('content'));
        $post->setPostTime(date("Y-m-d H:i:s"));
        $post->setUpdatedAt(date("Y-m-d H:i:s"));

        return $this->getBuzzService()->savePost($post);
    }

    /**
     * save share to the database
     * @param Post $post
     * @return share
     */
    public function saveShare($post) {
        $share = $this->setShare($post);
        return $this->getBuzzService()->saveShare($share);
    }

    /**
     * set share details
     * @param post $post
     * @return Share
     */
    public function setShare($post) {
        $share = new Share();
        $share->setPostId($post->getId());
        $share->setEmployeeNumber($post->getEmployeeNumber());
        $share->setNumberOfComments(0);
        $share->setNumberOfLikes(0);
        $share->setNumberOfUnlikes(0);
        $share->setShareTime(date("Y-m-d H:i:s"));
        $share->setUpdatedAt(date("Y-m-d H:i:s"));
        $share->setType(0);
        return $share;
    }

    /**
     * create link 
     * @param type $share
     * @param type $postLinkAddress
     * @param type $linkTitle
     * @param type $linkText
     * @return \Link
     */
    private function setLink($share) {
        $link = new Link();
        $link->setPostId($share->getPostId());
        $link->setLink($this->getValue('linkAddress'));
        $link->setType(1);
        return $link;
    }

    /**
     * save links to database
     * @param type $link
     * @return type
     */
    private function saveLink($link) {
        return $this->getBuzzService()->saveLink($link);
    }

}
