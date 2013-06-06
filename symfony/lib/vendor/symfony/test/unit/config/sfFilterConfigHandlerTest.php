<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(8);

$handler = new sfFilterConfigHandler();
$handler->initialize();

$dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'sfFilterConfigHandler'.DIRECTORY_SEPARATOR;

// parse errors
$t->diag('parse errors');
$files = array(
  $dir.'no_class.yml',
);

try
{
  $data = $handler->execute($files);
  $t->fail('filters.yml must have a "class" section for each filter entry');
}
catch (sfParseException $e)
{
  $t->like($e->getMessage(), '/with missing class key/', 'filters.yml must have a "class" section for each filter entry');
}

// no execution/rendering filter
foreach (array('execution', 'rendering') as $key)
{
  $files = array(
    $dir.sprintf('no_%s.yml', $key),
  );

  try
  {
    $data = $handler->execute($files);
    $t->fail(sprintf('filters.yml must have a filter of type "%s"', $key));
  }
  catch (sfParseException $e)
  {
    $t->like($e->getMessage(), sprintf('/must register a filter of type "%s"/', $key), sprintf('filters.yml must have a filter of type "%s"', $key));
  }
}

// filter inheritance
$t->diag('filter inheritance');
$files = array(
  $dir.'default_filters.yml',
  $dir.'not_disabled.yml',
);

try
{
  $data = $handler->execute($files);
  $t->fail('filters.yml must keep all filters when inheriting from a master filters configuration file');
}
catch (sfConfigurationException $e)
{
  $t->like($e->getMessage(), '/but not present/', 'filters.yml must keep all filters when inheriting from a master filters configuration file');
}

// disabling a filter
$t->diag('disabling a filter');
$files = array(
  $dir.'disable.yml',
);

$t->unlike($handler->execute($files), '/defaultFilterClass/', 'you can disable a filter by settings "enabled" to false');

// condition support
$t->diag('condition support');
$files = array(
  $dir.'condition.yml',
);

sfConfig::set('default_test', true);
$t->like($handler->execute($files), '/defaultFilterClass/', 'you can add a "condition" key to the filter parameters');

sfConfig::set('default_test', false);
$t->unlike($handler->execute($files), '/defaultFilterClass/', 'you can add a "condition" key to the filter parameters');

// usual configuration
$t->diag('usual configuration');
$files = array(
  $dir.'default_filters.yml',
  $dir.'filters.yml',
);

$data = $handler->execute($files);
$data = preg_replace('#date\: \d+/\d+/\d+ \d+\:\d+\:\d+\n#', '', $data);
$t->is($data, str_replace("\r\n", "\n", file_get_contents($dir.'result.php')), 'core filters.yml can be overriden');
