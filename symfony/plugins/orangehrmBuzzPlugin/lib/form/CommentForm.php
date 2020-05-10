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
 * From to add a Task for an Emloyee
 *
 * @author aruna
 */
class CommentForm extends sfForm {

    private $widgets;

    /**
     * 
     * @return BuzzService
     */
    protected function getBuzzService() {
        if (!$this->buzzService instanceof BuzzService) {
            $this->buzzService = new BuzzService();
        }
        return $this->buzzService;
    }

    /**
     * Defining widgets and thier default values
     */
    public function configure() {
        $this->widgets = array(
            'comment' => new sfWidgetFormTextarea(
                    array(), array('rows' => '4', 'columns' => '80', 'id' => 'commentBox')
            ),
            'shareId' => new sfWidgetFormInput(
                    array(), array('hidden' => 'hidden')
            ),
        );
        $this->setWidgets($this->widgets);
        $this->widgetSchema->setNameFormat('createComment[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
        $this->assignValidators();
    }

    /**
     * Defining the validators for the widgets
     */
    public function assignValidators() {
        $this->setValidators(array(
            'comment' => new sfValidatorString(array('required' => true)),
            'shareId' => new sfValidatorString(array('required' => true))
        ));
    }

    /**
     * Get the label texts for the form widgets
     * @return array Label Texts
     */
    protected function getFormLabels() {

        $labels = array(
            'comment' => __('Write a comment...')
        );
        return $labels;
    }

    /**
     * set valuves to the comment
     * @return Comment
     */
    public function saveComment($loggedInEmployeeNumber, $employee) {
        $comment = new Comment();
        $comment->setShareId($this->getValue('shareId'));
        $comment->setEmployeeNumber($loggedInEmployeeNumber);
        $comment->setCommentText($this->getValue('comment'));
        $comment->setCommentTime(date("Y-m-d H:i:s"));
        $comment->setUpdatedAt(date("Y-m-d H:i:s"));
        $comment->setNumberOfLikes(0);
        $comment->setNumberOfUnlikes(0);
        return $this->getBuzzService()->saveCommentShare($comment);
    }

}
