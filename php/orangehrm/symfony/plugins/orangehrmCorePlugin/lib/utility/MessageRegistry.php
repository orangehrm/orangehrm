<?php

class MessageRegistry {
    const APPEND = 1;
    const PREPEND = 2;
    const REPLACE_ALL = 3;
    const REPLACE_AT = 4;

    private static $instance;
    private $messages = array();

    /**
     *
     * @return MessageRegistry
     */
    public static function instance() {
        if (!(self::$instance instanceof MessageRegistry)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addMessage($message, $module, $action, $mode = self::APPEND, $replaceAt = 0) {
        if (!isset($this->messages[$module])) {
            $this->messages[$module] = array();
        }

        if (!isset($this->messages[$module][$action])) {
            $this->messages[$module][$action] = array();
        }

        switch ($mode) {
            case self::PREPEND:
                array_unshift($this->messages[$module][$action], $message);
                break;
            case self::REPLACE_AT:
                $this->messages[$module][$action][$replaceAt] = $message;
                break;
            case self::REPLACE_ALL:
                $this->messages[$module][$action] = array($message);
                break;
            case self::APPEND:
            default:
                $this->messages[$module][$action][] = $message;
                break;
        }
    }

    public function getMessage($module, $action, $formatted = true, $separator = ' ') {
//        echo $module, $action;
//        print_r($this->messages[$module][$action]);
        if (!isset($this->messages[$module][$action])) {
            $this->messages[$module][$action] = array();
        }

        return ($formatted) ? implode($separator, $this->messages[$module][$action]) : $this->messages[$module][$action];
    }

}