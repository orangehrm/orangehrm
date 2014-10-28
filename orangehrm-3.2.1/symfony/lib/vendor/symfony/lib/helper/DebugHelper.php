<?php

function log_message($message, $priority = 'info')
{
  if (sfConfig::get('sf_logging_enabled'))
  {
    sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent(null, 'application.log', array($message, 'priority' => constant('sfLogger::'.strtoupper($priority)))));
  }
}
