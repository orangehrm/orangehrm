<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(2, new lime_output_color());

$config = new sfSimpleYamlConfigHandler();
$config->initialize();

$dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'sfSimpleYamlConfigHandler'.DIRECTORY_SEPARATOR;

$array = get_retval($config, array($dir.'config.yml'));
$t->is($array['article']['title'], 'foo', '->execute() returns configuration file as an array');

$array = get_retval($config, array($dir.'config.yml', $dir.'config_bis.yml'));
$t->is($array['article']['title'], 'bar', '->execute() returns configuration file as an array');

function get_retval($config, $files)
{
  $retval = $config->execute($files);
  $retval = preg_replace('#^<\?php#', '', $retval);
  $retval = preg_replace('#<\?php$#s', '', $retval);

  return eval($retval);
}
