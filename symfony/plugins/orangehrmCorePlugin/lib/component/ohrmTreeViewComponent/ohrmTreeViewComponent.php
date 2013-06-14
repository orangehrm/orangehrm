<?php

class ohrmTreeViewComponent extends ohrmComponent {

    private $deleteRestrictionLevels;
    private $addRestrictionLevels;
    private $allowAdd = true;
    private $allowDelete = true;
    private $type = null;

    public function __construct($type = null) {
        $this->type = $type;
        $this->configure();
    }

    public function setPropertyObject(ohrmTreeViewComponentProperty $propertyObject) {
        $this->propertyObject = $propertyObject;
    }

    /**
     *
     * @return ohrmTreeViewComponentProperty
     */
    public function getPropertyObject() {
        if (!(isset($this->propertyObject) && $this->propertyObject instanceof ohrmTreeViewComponentProperty)) {
            $this->propertyObject = new ohrmTreeViewComponentProperty();
        }
        return $this->propertyObject;
    }

    public function render($output = true) {
        $html = '';

        $html .= content_tag('div', $this->getPropertyObject()->getRootLabel(), array('id' => 'ohrmTreeViewComponent_TreeHeader'));
        $html .= $this->_drawTree();

        if ($output) {
            echo $html;
            return true;
        } else {
            return $html;
        }
    }

    public function configure() {

    }

    private function _drawTree() {
        $tree = $this->getPropertyObject()->getTreeObject()->fetchTree();

        $html = '';
        $levels = array();

        $lastLevel = 0;

        foreach ($tree as $node) {

            if (($node ['level'] == $lastLevel) && ($lastLevel > 0)) {
                $html .= '</li>' . "\n";
            }

            if ($node ['level'] > $lastLevel) {
                $html .= ( $node['level'] == 1) ? '<ul id="ohrmTreeViewComponent_Tree">' : '<ul>';
            }

            if ($node ['level'] < $lastLevel) {
                $html .= str_repeat("</li>\n</ul>\n", $lastLevel - $node ['level']) . '</li>' . "\n";
            }

            $html .= $this->_getListItemHtml($node);

            //Refreshing last level of the item
            $lastLevel = $node ['level'];
        }

        return $html;
    }

    private function _getListItemHtml($node) {
        if (!isset($this->deleteRestrictionLevels)) {
            $this->deleteRestrictionLevels = $this->getPropertyObject()->getDeleteReistrictionLevels();
            $this->addRestrictionLevels = $this->getPropertyObject()->getAddReistrictionLevels();

            $this->allowAdd = !(isset($this->addRestrictionLevels[0]) && ($this->addRestrictionLevels[0] === '*'));
            $this->allowDelete = !(isset($this->deleteRestrictionLevels[0]) && ($this->deleteRestrictionLevels[0] === '*'));
        }

        $listContent = '';
        $nodeName = isset($node['name']) ? __($node['name']) : __($node->getName());
        $nodeUnitId = $node->getUnitId();
        $displayNodeName = (!empty($nodeUnitId)) ? $nodeUnitId." : ".$nodeName : $nodeName;
        $displayNodeName = escape_once($displayNodeName);
        $nodeDescription = isset($node['description']) ? __($node['description']) : __($node->getDescription());
        $displayNodeDescription = escape_once($nodeDescription);
        
        if (!empty($nodeDescription)) {
            $listContent .= "<span id=\"span_{$node['id']}\" class=\"labelNode tiptip\" title=\"$displayNodeDescription\">$displayNodeName</span>";
        } else {
            $listContent .= "<span id=\"span_{$node['id']}\" class=\"labelNode\">$displayNodeName</span>";
        }

        $listContent .= content_tag('a', $displayNodeName, array(
                    'href' => '#?',
                    'id' => 'treeLink_edit_' . $node['id'],
                    'class' => 'editLink'
                ));

        $listContent .= '&nbsp;';

        if ($this->allowAdd && !in_array($node['level'], $this->addRestrictionLevels)) {
            $listContent .= content_tag('a', ' +', array(
                        'href' => '#?',
                        'id' => 'treeLink_addChild_' . $node['id'],
                        'style' => 'text-decoration: none;',
                        'class' => 'addButton'
                    ));
        }

        $listContent .= '&nbsp;';

        if ($node['id'] != 1 && $this->allowDelete && !in_array($node['level'], $this->deleteRestrictionLevels)) {
            $listContent .= content_tag('a', ' x', array(
                        'href' => '#?',
                        'id' => 'treeLink_delete_' . $node['id'],
                        'style' => 'text-decoration: none;',
                        'class' => 'deleteButton'
                    ));
        }

        return "<li id=\"node_{$node['id']}\">" . $listContent;
    }

    /**
     * @deprecated
     */
    private function _drawTestTree() {
        $listItems = '';
        for ($i = 1; $i <= 5; $i++) {
            $subListItems = array();
            for ($j = 1; $j <= rand(0, 8); $j++) {
                $subListItems[] = content_tag('li', "\n\t" . content_tag('a', "Sub List Item {$i}-{$j}", array('href' => "?#{$i}-{$j}"))) . "\n";
            }
            $subListItems = (count($subListItems) > 0) ? content_tag('ul', "\n" . implode('', $subListItems)) : '';
            $listItems[] = content_tag('li', "\n\t" . content_tag('a', "Item {$i}", array('href' => "?#{$i}")) . "{$subListItems}");
        }
        $listItems = "\n" . implode('', $listItems);

        $html = '';
        $html .= content_tag('script', '', array(
                    'type' => 'text/javascript',
                    'src' => javascript_path('jquery.treeview.min.js'),
                ));
        $html .= tag('link', array(
                    'rel' => 'stylesheet',
                    'href' => stylesheet_path('jquery-treeview/jquery.treeview.css'),
                ));

        $html .= content_tag('a', 'Collapse All', array('href' => '?#'));
        $html .= ' | ';
        $html .= content_tag('a', 'Expand All', array('href' => '?#'));
        $html = content_tag('div', $html, array('id' => 'ohrmTreeViewComponent_TreeController', 'class' => 'treecontrol'));

        $html .= content_tag('div', $this->getPropertyObject()->getRootLabel(), array('id' => 'ohrmTreeViewComponent_TreeHeader'));
        $html .= content_tag('ul', $listItems, array('id' => 'ohrmTreeViewComponent_Tree'));

        return $html;
    }

    public function printJavascript() {
        $this->addScriptContent("
            $('#ohrmTreeViewComponent_Tree').treeview({
                collapsed: false,
                control:'#ohrmTreeViewComponent_TreeController',
                persist: 'location'
            });

            $('#ohrmTreeViewComponent_Tree *').css('list-style', 'none'); // TODO: Move this to a stylesheet. Make sure to test in IE

            $('a[id^=\"treeLink_edit_\"]').click(function() {
                loadNode(parseInt($(this).attr('id').replace('treeLink_edit_', '')));
                _clearMessage();
            });

            $('a[id^=\"treeLink_addChild_\"]').click(function() {
                addChildToNode(parseInt($(this).attr('id').replace('treeLink_addChild_', '')));
            });

            $('a[id^=\"treeLink_delete_\"]').click(function() {
                deleteNode(parseInt($(this).attr('id').replace('treeLink_delete_', '')));
            });

            clearForm();
");

        $this->addScriptFunction("
            $('form[id^=\"ohrmFormComponent_Form\"]').each(function() {
                $(this).parent().parent().show();
            });
            $('.requirednotice').show();
        ", 'showForm');

        $this->addScriptFunction("
            $('form[id^=\"ohrmFormComponent_Form\"]').each(function() {
                $(this).parent().parent().hide();
            });
            $('.requirednotice').hide();
        ", 'hideForm');

        $this->addScriptFunction("
            $('form[id^=\"ohrmFormComponent_Form\"] :input').filter(':not([type=\"hidden\"])').val('');
            $('.idValueLabel').html('');
            $('#lblParentNotice').remove();
        ", 'clearForm');

        $html = '';

        $html .= javascript_include_tag('jquery/jquery.treeview.min.js');

        if ($this->type == null) {
            $html .= tag('link', array(
                        'rel' => 'stylesheet',
                        'href' => theme_path('css/jquery/jquery-treeview/jquery.treeview.css'),
                    ));
        } else {
            $html .= tag('link', array(
                        'rel' => 'stylesheet',
                        'href' => theme_path('css/jquery/jquery-treeview/jquery.treeview_1.css'),
                    ));
        }
        $this->addScriptContent(array('$(document).ready(function() {', '});'), 'wrap');
        $this->addScriptContent($this->getScriptFunctionsString());
        $this->addScriptContent(array('//<![CDATA[', '//]]>'), 'wrap');

        $html .= content_tag('script', $this->scriptContent, array('type' => 'text/javascript'));

        echo $html;
        return true;
    }

}
