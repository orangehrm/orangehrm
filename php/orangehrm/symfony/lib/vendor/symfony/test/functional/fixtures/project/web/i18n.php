<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('i18n', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
