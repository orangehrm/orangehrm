<?php

class ohrmFormComponent {

    private $object;
    private $componentService;
    private $propertyObject;
    private $hasFormNavigatorBar = true;
    private $hasFormTitle = true;
    private $hasFormActionBar = true;
    protected $identifierValue = null;
    private $actions = array();
    private $scriptContent = '';
    private $scriptUrls = array();
    private $externalJavacripts = array();
    private $externalStylesheets = array();
    private $scriptFunctions = array();
    private $scriptFunctionParams = array();

    public function __construct() {
        $this->configure();
    }

    public function setPropertyObject(ohrmFormComponentProperty $propertyObject) {
        $this->propertyObject = $propertyObject;
    }

    public function getPropertyObject() {
        if (!(isset($this->propertyObject) && $this->propertyObject instanceof ohrmFormComponentProperty)) {
            $this->propertyObject = new ohrmFormComponentProperty();
        }
        return $this->propertyObject;
    }

    public function hasFormActionBar($hasFormActionBar = null) {
        if (empty($hasFormActionBar)) {
            return $this->hasFormActionBar;
        } else {
            $this->hasFormActionBar = $hasFormActionBar;
        }
    }

    public function render($output = true) {
        $this->_loadObject();

        $ohrmForm = new ohrmForm();
        $ohrmForm->setObject($this->object);
        $ohrmForm->setPropertyObject($this->getPropertyObject());

        $this->hasFormActionBar = sfContext::getInstance()->getUser()->getAttribute('ohrmComponent.editMode', true);

        if ($this->hasFormActionBar) {
            $ohrmForm = new ohrmFormActionBar($ohrmForm);
        }

        $html = $ohrmForm->getHtml();

        $html = $this->_getNavigationHtml() . $html;

        $html = $this->getExternalIncludes() . $html;

        if ($output) {
            echo $html;
            return true;
        } else {
            return $html;
        }
    }

    public function getIdentifierValue() {
        return $this->identifierValue;
    }

    public function setIdenfierValue($identifierValue) {
        $this->identifierValue = $identifierValue;
    }

    public function configure() {
        
    }

    public function postConfigure() {
        return true;
    }

    public function printJavascript() {

        if ($this->hasFormNavigatorBar) {
            $backButtonUrl = public_path($this->getPropertyObject()->getBackButtonUrl());

            $this->addScriptContent(<<<EOT
        $('#ohrmFormNavigatorButton_Back').click(function() {
            location.href = '{$backButtonUrl}';
        });

EOT
            );
        }

        if ($this->hasFormActionBar) {

            $saveAction = public_path($this->actions['saveAction']);

            $this->addScriptContent(<<<EOT
        $('#ohrmFormActionButton_Save').click(function() {
            $('#ohrmFormComponent_Form').attr('action', '{$saveAction}');
            $('#ohrmFormComponent_Form').submit();
              $("#unitDialog").dialog("close");
        });

        $('#ohrmFormActionButton_AddAsNew').click(function() {
        });

        $('#ohrmFormActionButton_Reset').click(function() {
            $('#ohrmFormComponent_Form').get(0).reset();
            $("label.error[generated='true']").each(function() {
                $('#' + $(this).attr('for')).removeClass('error');
                $(this).remove();
            });
        });
    $('#ohrmFormActionButton_Cancel').click(function() {
    $("#unitDialog").dialog("close");
        });


EOT
            );
        }

        $functions = $this->scriptFunctions;
        foreach ($functions as $identifier => $content) {
            $functionName = is_numeric($identifier) ? "function_{$identifier}" : $identifier;

            if (is_numeric($identifier)) {
                $paramList = '';
            } else {
                $paramList = isset($this->scriptFunctionParams[$identifier]) ? implode(', ', $this->scriptFunctionParams[$identifier]) : '';
            }

            $functions[$identifier] = "function {$functionName}({$paramList}) {
                {$content}
            }\n\n";
        }


        $this->addScriptContent(array('$(document).ready(function() {', '});'), 'wrap');
        $this->addScriptContent(implode('', $functions));
        $this->addScriptContent(array('//<![CDATA[', '//]]>'), 'wrap');

        foreach ($this->scriptUrls as $key => $url) {
            $this->scriptUrls[$key] = public_path($url, true);
        }
        $this->scriptContent = preg_replace(array_keys($this->scriptUrls), $this->scriptUrls, $this->scriptContent);

        echo content_tag('script', $this->scriptContent, array('type' => 'text/javascript'));

        return true;
    }

    public function printRequiredFieldsNotice() {
        $content = content_tag('span', '* ', array('class' => 'required'));
        echo content_tag('div', $content. __(CommonMessages::REQUIRED_FIELD), array('class' => 'requirednotice'));
        return true;
    }

    public function getActions() {
        return $this->actions;
    }

    public function setActions(array $actions) {
        $this->actions = $actions;
    }

    public function addAction($actionIdentifier, $action) {
        if (!array_key_exists($actionIdentifier, $this->actions)) {
            $this->actions[$actionIdentifier] = $action;
        } else {
            throw new ohrmFormComponentException('Action is already set');
        }
    }

    public function removeAction($actionIdentifier) {
        if (array_key_exists($actionIdentifier, $this->actions)) {
            unset($this->actions[$actionIdentifier]);
        } else {
            throw new ohrmFormComponentException('Specified action cannot be found');
        }
    }

    public function replaceAction($actionIdentifier, $action) {
        if (array_key_exists($actionIdentifier, $this->actions)) {
            $this->actions[$actionIdentifier] = $action;
        } else {
            throw new ohrmFormComponentException('Specified action cannot be found');
        }
    }

    public function hasFormNavigatorBar($hasFormNavigatorBar = null) {
        if (!is_null($hasFormNavigatorBar)) {
            $this->hasFormNavigatorBar = (bool) $hasFormNavigatorBar;
        } else {
            return $this->hasFormNavigatorBar;
        }
    }

    public function addScriptContent($scriptContent, $mode = 'append') {
        if ($mode === 'append') {
            $this->scriptContent .= $scriptContent;
        } elseif ($mode === 'prepend') {
            $this->scriptContent = $scriptContent . $this->scriptContent;
        } elseif ($mode === 'wrap') {
            list($front, $back) = $scriptContent;
            $this->scriptContent = "{$front}\n{$this->scriptContent}\n{$back}";
        }
        return true;
    }

    public function addScriptFunction($content, $identifier = null, $params = array()) {
        if ($identifier === null) {
            array_push($this->scriptFunctions, $content);
        } else {
            $this->scriptFunctions[$identifier] = $content;
            $this->scriptFunctionParams[$identifier] = $params;
        }
    }

    public function addScriptUrl($key, $url) {
        $this->scriptUrls['/\{' . $key . '\}/'] = $url;
    }

    public function addExternalJavascript($path) {
        $key = md5($path);
        $this->externalJavacripts[$key] = $path;
    }

    public function addExternalStylesheet($path) {
        $key = md5($path);
        $this->externalStylesheets[$key] = $path;
    }

    private function _loadObject() {
        $object = $this->getPropertyObject()->getObject();

        $service = $this->getPropertyObject()->getService();
        $method = $this->getPropertyObject()->getMethod();
        $params = $this->getPropertyObject()->getParameters();

        if (is_object($object)) {
            $this->object = $object;
        } elseif (is_object($service) && !empty($method)) {
            $this->object = @call_user_method_array($method, $service, $params);
        } else {
            throw new ohrmFormComponentException();
        }
    }

    private function _getNavigationHtml() {
        $html = '';

        if ($this->hasFormNavigatorBar) {
            $html .= tag('input', array(
                        'type' => 'button',
                        'value' => 'Back',
                        'id' => 'ohrmFormNavigatorButton_Back',
                        'class' => 'plainbtn'
                    ));
            $html = content_tag('div', $html, array('style' => 'margin: 6px 0px 4px 16px;'));
        }

        return $html;
    }

    protected final function getExternalIncludes() {
        $html = '';

        foreach ($this->externalJavacripts as $jsPath) {
            $html .= javascript_include_tag($jsPath);
        }

        foreach ($this->externalStylesheets as $stylesheetPath) {
            $html .= stylesheet_tag($stylesheetPath);
        }

        return $html;
    }

}