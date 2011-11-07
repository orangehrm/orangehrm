<?php

abstract class ohrmComponent {

    protected $scriptContent = '';
    protected $scriptFunctions = array();
    protected $scriptFunctionParams = array();

    public function addScriptFunction($content, $identifier = null, $params = array()) {
        if ($identifier === null) {
            array_push($this->scriptFunctions, $content);
        } else {
            $this->scriptFunctions[$identifier] = $content;
            $this->scriptFunctionParams[$identifier] = $params;
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

    protected function getScriptFunctionsString() {
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
        return "\n\n" . implode('', $functions);
    }

}

