<?php

class ohrmFormActionBar extends ohrmFormDecorator {
    private $hasAddAsNewAction = false;

    public function  getHtml() {
        $actionBarHtml = '';

        $actionBarHtml .= tag('input', array(
            'type' => 'button',
            'value' => 'Save',
            'id' => 'ohrmFormActionButton_Save',
            'name' => 'btnSave',
            'class' => 'plainbtn',
        ));

        $actionBarHtml .= "\n";

        if ($this->hasAddAsNewAction) {
            $actionBarHtml .= tag('input', array(
                'type' => 'button',
                'value' => 'Add as New',
                'id' => 'ohrmFormActionButton_AddAsNew',
                'name' => 'btnAddAsNew',
                'class' => 'longbtn',
            ));

            $actionBarHtml .= "\n";
        }

        $actionBarHtml .= tag('input', array(
            'type' => 'button',
            'value' => 'Cancel',
            'id' => 'ohrmFormActionButton_Cancel',
            'name' => 'btnCancel',
            'class' => 'plainbtn',
        ));

        $actionBarHtml .= "\n";

        //$actionBarHtml = content_tag('div', $actionBarHtml, array('style' => 'width: 98%; border-top: solid 1px #FAD163; margin: 10px 0px 0px 2px; padding: 4px;'));
        $actionBarHtml = content_tag('div', $actionBarHtml, array('style' => 'width: 180px; padding-left:260px'));
        $actionBarHtml .= tag('br', array('class' => 'clear'));
        
        return $this->decoratedForm->getHtml() . $actionBarHtml;
    }

    public function hasAddAsNewAction($hasAddAsNewAction = null) {
        if (is_null($hasAddAsNewAction)) {
            return $this->hasAddAsNewAction;
        } else {
            $this->hasAddAsNewAction = (bool) $hasAddAsNewAction;
        }
    }
}
