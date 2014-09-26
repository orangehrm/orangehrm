<?php

class MessageRegistry {
    const APPEND = 1;
    const PREPEND = 2;
    const REPLACE_ALL = 3;
    const REPLACE_AT = 4;

    private static $instance;

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
        $sfUser = sfContext::getInstance()->getUser();

        if (is_null($sfUser->getAttribute('message', null, 'system'))) {
            $sfUser->setAttribute('message', array(), 'system');
            $messageContainer = array();
        } else {
            $messageContainer = $sfUser->getAttribute('message', array(), 'system');
        }

        if (!isset($messageContainer[$module])) {
            $messageContainer[$module] = array();
        }

        if (!isset($messageContainer[$module][$action])) {
            $messageContainer[$module][$action] = array();
        }

        switch ($mode) {
            case self::PREPEND:
                array_unshift($messageContainer[$module][$action], $message);
                break;
            case self::REPLACE_AT:
                $messageContainer[$module][$action][$replaceAt] = $message;
                break;
            case self::REPLACE_ALL:
                $messageContainer[$module][$action] = array($message);
                break;
            case self::APPEND:
            default:
                $messageContainer[$module][$action][] = $message;
                break;
        }

        $sfUser->setAttribute('message', $messageContainer, 'system');
    }

    public function getMessage($module, $action, $formatted = true, $separator = '; ') {
        $message = sfContext::getInstance()->getUser()->getAttribute('message', array(), 'system');

        if (!isset($message[$module][$action])) {
            $message[$module][$action] = array();
        }

        MessageRegistry::instance()->flushMessage();

        return ($formatted) ? implode($separator, $message[$module][$action]) : $message[$module][$action];
    }

    public function flushMessage() {
        sfContext::getInstance()->getUser()->setAttribute('message', array(), 'system');
    }

}