<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('cache', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
