<?php

function debug_message($message)
{
  if (sfConfig::get('sf_web_debug') && sfConfig::get('sf_logging_enabled'))
  {
    sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent(null, 'application.log', array('This feature is deprecated in favor of the log_message helper.', 'priority' => sfLogger::ERR)));
  }
}

function log_message($message, $priority = 'info')
{
  if (sfConfig::get('sf_logging_enabled'))
  {
    sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent(null, 'application.log', array($message, 'priority' => constant('sfLogger::'.strtoupper($priority)))));
  }
}
