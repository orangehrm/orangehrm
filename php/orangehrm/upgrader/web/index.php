<?php


require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

if (!is_writable(dirname(__FILE__).'/../cache') || !is_writable(dirname(__FILE__).'/../log')) {
    die("'upgrader/cache' and 'upgrader/log' directories should be writable.");
}

$configuration = ProjectConfiguration::getApplicationConfiguration('upgrader', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
